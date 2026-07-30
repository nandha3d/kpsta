<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Gallery_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @param type $data
     * @return type
     */
    public function addAlbum($data) {
        $data['gu_id'] = md5(uniqid(mt_rand()));
        $this->db->insert('gallery_album', $data);
        return $this->db->insert_id();
    }

    public function getAllAlbum($param = false) {
        $this->db->select('a.name, a.id, a.description, a.created_at, a.gu_id as guId, count(i.id) as iCount, im.image as coverImage');
        $this->db->from('gallery_album a');
        $this->db->join('gallery_images i', 'a.gu_id = i.album_id', 'left');
        $this->db->join('gallery_images im', 'a.gu_id = im.album_id AND im.is_cover = 1', 'left');
        if (isset($param['year']) && $param['year']) {
            $this->db->where('a.created_at >=', $param['year'] . '-01-01 00:00:00');
            $this->db->where('a.created_at <=', $param['year'] . '-12-31 23:59:59');
        }

        if (isset($param['albumId']) && $param['albumId']) {
            $this->db->where('a.gu_id', $param['albumId']);
        }

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where('a.is_publish', 1);
            $this->db->having('iCount > 0');
        }

        $this->db->group_by(array('a.id', 'im.image'));
        $this->db->order_by('a.created_at desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllImages($param = false) {
        $this->db->select('a.name, a.id, a.description,a.gu_id as guId,  i.image, i.title ');
        $this->db->from('gallery_images i');
        $this->db->join('gallery_album a', 'a.gu_id = i.album_id', 'left');
        if (isset($param['year']) && $param['year']) {
            $this->db->where('a.created_at >=', $param['year'] . '-01-01 00:00:00');
            $this->db->where('a.created_at <=', $param['year'] . '-12-31 23:59:59');
        }

        if (isset($param['albumId']) && $param['albumId']) {
            $this->db->where('a.gu_id', $param['albumId']);
        }

        if (isset($param['isPublish']) && $param['isPublish']) {
            $this->db->where('a.is_publish', 1);
        }

        $this->db->order_by('a.created_at desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    public function getAllAlbumCount() {
        $this->db->select('count(id) as count');
        $query = $this->db->get('gallery_album');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getAlbum($id) {
        $this->db->select('*');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('gallery_album');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function update($id, $formValues) {
        unset($formValues['id']);
        $this->db->where(array("id" => $id));
        if ($this->db->update('gallery_album', $formValues)) {
            return true;
        }
        return false;
    }

    public function publish($id, $publish) {
        $this->db->where(array("id" => $id));
        if ($this->db->update('news', array('publish' => $publish))) {
            return true;
        }
        return false;
    }

    ############################################################################
    /**
     * 
     * @param type $data
     * @return type
     */

    public function addImage($data) {
        if ($this->getImagesCount($data['album_id']) == 0) {
            $data['is_cover'] = 1;
        }

        $this->db->insert('gallery_images', $data);
        return $this->db->insert_id();
    }

    public function getImagesCount($albumId) {
        $this->db->select('count(id) as count');
        $this->db->where(array("album_id" => $albumId));
        $query = $this->db->get('gallery_images');
        $result = $query->row(0, 'array');

        return $result['count'];
    }

    public function getAlbumImages($param) {
        $this->db->from('gallery_images');
        $this->db->where(array("album_id" => $param['albumId']));
        $this->db->order_by('is_cover desc, id desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function makeAlbumCover($albumId, $imageId) {
        $this->db->where(array("album_id" => $albumId));
        $this->db->where(array("is_cover" => 1));
        $this->db->update('gallery_images', array('is_cover' => 0));

        $this->db->where(array("album_id" => $albumId));
        $this->db->where(array("id" => $imageId));
        if ($this->db->update('gallery_images', array('is_cover' => 1))) {
            return true;
        }
        return false;
    }

    public function getImage($albumId, $imageId) {
        $this->db->select('*');
        $this->db->where(array("id" => $imageId));
        $this->db->where(array("album_id" => $albumId));
        $query = $this->db->get('gallery_images');

        if ($query->num_rows() > 0) {
            return $query->row(0, 'array');
        }
        return false;
    }

    public function deleteImage($albumId, $imageId) {

        $this->db->where(array("id" => $imageId));
        $this->db->where(array("album_id" => $albumId));
        if ($this->db->delete('gallery_images')) {
            return true;
        }
        return false;
    }

    public function deleteAlbum($id) {
        $this->db->select('gu_id');
        $this->db->where(array("id" => $id));
        $query = $this->db->get('gallery_album');
        if ($query->num_rows() > 0) {
            $album = $query->row(0, 'array');
            // Delete associated images first
            $this->db->where(array("album_id" => $album['gu_id']));
            $this->db->delete('gallery_images');
        }

        $this->db->where(array("id" => $id));
        if ($this->db->delete('gallery_album')) {
            return true;
        }
        return false;
    }

}
