<?php

namespace App\Models;
use MF\Model\Model;
use PDO;

class Photographs extends Model{

    private $id_photo;
    private $nome_photo;
    private $desc_photo;
    private $link_photo;

    public function __get($att){
        return $this->$att;
    }

    public function __set($att, $value){
        return $this->$att = $value;
    }

    public function registerPhotograph(){
        $stmt = $this->db->prepare('INSERT INTO foto_moradores(nome_foto, descricao_foto, link_foto) values (:nome, :descr, :link)');
        if($this->desc_photo == NULL){
            $stmt = $this->db->prepare('INSERT INTO foto_moradores(nome_foto, link_foto) values (:nome, :link)');
        }else {
            $stmt->bindValue(":descr", $this->desc_photo);
        }
        $stmt->bindValue(":nome", $this->nome_photo);
        $stmt->bindValue(":link", $this->link_photo.$this->nome_photo);
        $stmt->execute();
        $stmt = $this->db->prepare('SELECT id_foto FROM foto_moradores WHERE nome_foto = :novo_nome');
        $stmt->bindValue(":novo_nome", $this->nome_photo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateFK($id_resident, $newFK){
        $stmt = $this->db->prepare("UPDATE moradores SET foto_fk = :fk WHERE id_morador = :id");
        $stmt->bindValue(':fk', $newFK);
        $stmt->bindValue(':id', $id_resident);
        $stmt->execute();
    }

    public function getAllPhotographRegisters(){
        $stmt = $this->db->prepare("SELECT * FROM foto_moradores");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>