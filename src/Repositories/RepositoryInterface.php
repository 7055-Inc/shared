<?php


namespace The7055inc\Shared\Repositories;

/**
 * Interface RepositoryInterface
 * @package The7055inc\Shared\Repositories
 */
interface RepositoryInterface
{
    function find($ID);
    function query($where);
    function insert($data);
    function update($id, $data);
    function delete($id);
    function prepare($item);
}