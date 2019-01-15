<?php
/**
 * Magecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magecom.net so we can send you a copy immediately.
 *
 * @category Magecom
 * @package Magecom_Module
 * @copyright Copyright (c)  2019 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Migration\Console;

namespace Migration\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateSplitCommand
 * @package Migration\Console
 */
class MigrateSplitCommand extends AbstractMigrateCommand
{
    /**
     * @var \Migration\Mode\Split
     */
    private $splitMode;

    /**
     * MigrateSplitCommand constructor.
     * @param \Migration\Config $config
     * @param \Migration\Logger\Manager $logManager
     * @param \Migration\App\Progress $progress
     * @param \Migration\Mode\Split $splitMode
     */
    public function __construct(
        \Migration\Config $config,
        \Migration\Logger\Manager $logManager,
        \Migration\App\Progress $progress,
        \Migration\Mode\Split $splitMode
    ) {
        $this->splitMode = $splitMode;
        parent::__construct($config, $logManager, $progress);
    }

    /**
     * set cofig info
     */
    protected function configure()
    {
        $this->setName('migrate:split')
            ->setDescription('Main migration of split');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->splitMode->run();
    }
}