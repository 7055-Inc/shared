<?php


namespace The7055inc\Shared\Repositories;

/**
 * Class BaseRepository
 * @package The7055inc\Shared\Repositories
 */
class BaseRepository
{
    /**
     * Update post status
     *
     * @param $id
     * @param $status
     *
     * @return bool|int
     */
    public function update_post_status($id, $status)
    {
        global $wpdb;

        return $wpdb->update($wpdb->posts,
            array(
                'post_status'   => $status,
                'post_modified' => wp_date('Y-m-d H:i:s')
            ),
            array(
                'ID' => $id
            ),
            array(
                '%s', '%s'
            ),
            array('%d')
        );
    }
}