<?php

namespace Sandftae\CustomShipping\Api\General;

/**
 * Interface GeneratorInterface
 * @package Sandftae\CustomShipping\Api\General
 */
interface GeneratorInterface
{
    /**
     * @param array $data
     * @return \Generator
     */
    public function generator(array $data):\Generator;
}
