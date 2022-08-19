<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class PhotographController extends Action {

    public function photograph(){
        $photos = Container::getModel('Photographs');
        $moradores = Container::getModel('Residents');
        $id_resident = $_POST['id_resident_photo'];
        foreach($moradores->getAllResidentsRegisters() as $m){
            if($id_resident == $m['id_morador']){
                $photos->id_photo = $m['foto_fk'];
            }
        }
        foreach($photos->getAllPhotographRegisters() as $e){
            if($photos->id_photo == $e['id_foto']){
                $photos->nome_photo = $e['nome_foto'];
                $photos->desc_photo = $e['descricao_foto'];
                $photos->link_photo = $e['link_foto'];
            }
        }
        $_POST['id_photo'] = $photos->id_photo;
        if(str_contains($photos->nome_photo,'.png')){
            $newName = str_replace('.png', '', $photos->nome_photo);
        }
        $_POST['nome_photo'] = $newName;
        $_POST['desc_photo'] = $photos->desc_photo;

        $this->render('photograph');
    }

    public function deleteInChange(){
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $fotos = Container::getModel('Photographs');
        $fotos->id_photo = $data->id_photo;
        foreach($fotos->getAllPhotographRegisters() as $all){
            if($all['id_foto'] == $fotos->id_photo){
                if($fotos->id_photo != 1){
                    $reserved = $all['link_foto'];
                    $fotos->id_photo = 1;
                    $fotos->nome_photo = 'user.png';
                    $fotos->desc_photo = 'Changing...;;Aguarde';
                    unlink($_SERVER['DOCUMENT_ROOT'].$reserved);
                }
            }
        }
    }

    public function registerPhotograph(){
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $photos = Container::getModel('Photographs');

        $id_resident = $data->id_morador;
        $photos->nome_photo = $data->name_foto;
        if(str_contains($photos->nome_photo,'.png')){
            $photos->nome_photo = str_replace('.png', '*', $photos->nome_photo);
        }
        $photos->desc_photo = $data->descricao;
        $photos->link_photo = $data->link_foto;
        if(str_contains($photos->link_photo,'.png')){
            $photos->link_photo = str_replace('.png', '*', $photos->link_photo);
        }

        $TIN;
        $NV = false;
        for($TIN = 0; $TIN < 1; $TIN ++){
            foreach($photos->getAllPhotographRegisters() as $e){
                $replaced = str_replace('.png','',$e['nome_foto']);
                if($photos->nome_photo == $replaced || $photos->nome_photo == 'user'){
                    $photos->nome_photo = strval(rand());
                    $TIN = -1;
                    break;
                }
            }
            if($NV == true){
                break;
            }
            $photos->nome_photo = $photos->nome_photo.'.png';
            $NV = true;
        }
        if($photos->desc_photo == ""){
            $photos->desc_photo = NULL;
        }

        $fk = $photos->registerPhotograph();
        $mora = Container::getModel('Residents');
        foreach($mora->getAllResidentsRegisters() as $morador){
            if($morador['id_morador'] == $id_resident){
                foreach($fk as $result){
                    $photos->updateFK($id_resident, $result['id_foto']);
                }
            }
        }

        $photo_to_save = $data->image;
        $photo_to_save = str_replace('data:image/png;base64,', '', $photo_to_save);
        $photo_to_save = str_replace(' ','+', $photo_to_save);
        $img = base64_decode($photo_to_save);
        $file = 'img/residents_photos/'.$photos->nome_photo;
        $success = file_put_contents($file, $img);
    }

    public function viewPhotograph(){
        $photos = Container::getModel('Photographs');
        foreach($photos->getAllPhotographRegisters() as $p){
            if($p['id_foto'] == $_POST['id_photo']){
                $_POST['link_photo'] = $p['link_foto'];
                $_POST['nome_photo'] = $p['nome_foto'];
                $_POST['desc_photo'] = $p['descricao_foto'];
                $_POST['id_photo'] = $p['id_foto'];
            }
        }

        $this->render('view_photograph'); 
    }
}
?>