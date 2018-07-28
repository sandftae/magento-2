<?php

namespace PleaseWork\Workshop\Api\Data;

/**
 * Interface PostInterface
 * @package PleaseWork\Workshop\Api\Data
 */
interface PostInterface
{
    /**
     * @param $objectModel
     * @return mixed
     */
    public function save($objectModel);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $model
     * @param $id
     * @return mixed
     */
    public function delete($model, $id);

    /**
     * @return mixed
     */
    public function getList();
}
