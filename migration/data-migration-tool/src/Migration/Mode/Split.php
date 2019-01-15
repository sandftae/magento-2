<?php

namespace Migration\Mode;

use Migration\App\SetupDeltaLog;
use Migration\App\Mode\StepList;
use Migration\App\Progress;
use Migration\App\Step\RollbackInterface;
use Migration\Logger\Logger;
use Migration\Exception;
use Migration\Config;
use Migration\App\Mode\StepListFactory;

/**
 * Class Split
 * @package Migration\Mode
 */
class Split extends AbstractMode implements \Migration\App\Mode\ModeInterface
{
    const LVL_ERROR = 300;
    /**
     * @var string
     */
    protected $mode = 'split';

    /**
     * @var SetupDeltaLog
     */
    protected $setupDeltaLog;

    /**
     * @var Config
     */
    protected $configReader;

    /**
     * Split constructor.
     * @param Progress $progress
     * @param Logger $logger
     * @param \Migration\App\Mode\StepListFactory $stepListFactory
     * @param SetupDeltaLog $setupDeltaLog
     * @param Config $configReader
     */
    public function __construct(
        Progress        $progress,
        Logger          $logger,
        StepListFactory $stepListFactory,
        SetupDeltaLog   $setupDeltaLog,
        Config          $configReader
    ) {
        parent::__construct($progress, $logger, $stepListFactory);

        $this->setupDeltaLog    = $setupDeltaLog;
        $this->configReader     = $configReader;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function run():bool
    {
        $steps = $this->stepListFactory->create(['mode' => 'split']);
        $this->runIntegrity($steps);
//        $this->setupDeltalog();
        $dataS = $steps->getSteps();
        foreach ($steps->getSteps() as $stepName => $step) {
            if (empty($step['data'])) {
                continue;
            }
            $this->runData($step, $stepName);
            if (!empty($step['volume'])) {
                $this->runVolume($step, $stepName);
            }
        }
        $this->logger->info('Split completed');
        return true;
    }

    /**
     * @param StepList $steps
     * @throws Exception
     */
    protected function runIntegrity(StepList $steps):void
    {
        $autoResolve = $this->configReader->getOption(Config::OPTION_AUTO_RESOLVE);
        $result = true;
        foreach ($steps->getSteps() as $stepName => $step) {
            if (!empty($step['integrity'])) {
                $result = $this->runStage($step['integrity'], $stepName, 'integrity check (Split step)') && $result;
            }
        }
        if (!$result && !$autoResolve) {
            $msg = $this->logger->getMessages();
            
            if(!empty($msg[Split::LVL_ERROR][0])){
                $this->logger->warning($msg[Split::LVL_ERROR][0]);
            }
            throw new Exception('Integrity Check failed (Split step)');
        }
    }

    /**
     * @throws Exception
     */
    protected function setupDeltalog():void
    {
        if (!$this->runStage($this->setupDeltaLog, 'Stage', 'setup triggers')) {
            throw new Exception('Setup triggers failed');
        }
    }

    /**
     * @param array $step
     * @param $stepName
     * @throws Exception
     */
    protected function runData(array $step, $stepName):void
    {
        if (!$this->runStage($step['data'], $stepName, 'split migration')) {
            $this->rollback($step['data'], $stepName);
            throw new Exception('Split Migration failed');
        }
    }

    /**
     * @param array $step
     * @param $stepName
     * @throws Exception
     */
    protected function runVolume(array $step, $stepName):void
    {
        if (!$this->runStage($step['volume'], $stepName, 'volume check')) {
            $this->rollback($step['data'], $stepName);
            if ($this->configReader->getStep('delta', $stepName)) {
                $this->logger->warning('Volume Check (split process) failed');
            } else {
                throw new Exception('Volume Check (split process) failed');
            }
        }
    }

    /**
     * @param $stage
     * @param $stepName
     */
    protected function rollback($stage, $stepName):void
    {
        if ($stage instanceof RollbackInterface) {
            $this->logger->info('Error occurred. Rollback.');
            $this->logger->info(sprintf('%s: rollback', $stepName));
            try {
                $stage->rollback();
            } catch (\Migration\Exception $e) {
                $this->logger->error($e->getMessage());
            }
            $this->progress->reset($stage);
            $this->logger->info('Please fix errors and run Migration Tool again');
        }
    }
}