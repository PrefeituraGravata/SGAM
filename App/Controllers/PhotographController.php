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
        if(str_contains($photos->nome_photo,'.jpg')){
            $newName = str_replace('.jpg','',$photos->nome_photo);
        } else {
            $newName = str_replace('.png', '', $photos->nome_photo);
        }
        $_POST['nome_photo'] = $newName;
        $_POST['desc_photo'] = $photos->desc_photo;

        $this->render('photograph');
    }

    public function registerPhotograph(){
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $id_resident = $_POST['id_resident_photo'];

        $photos = Container::getModel('Photographs');
        //Tratando a duplicidade de nomes
        $photos->nome_photo = $data->name_foto;
        foreach($photos->getAllPhotographRegisters() as $e){
            $try = true;
            while($try){
                if($photos->nome_photo == $e['nome_foto']){
                    $photos->nome_photo = strval(rand()).'.png';
                }
                foreach($photos->getAllPhotographRegisters() as $e2){
                    if($photos->nome_photo == $e['nome_foto']){
                        $try = true;
                        break;
                    } else {
                        $try = false;
                    }
                }
            }
        }
        //#######################################
        $photos->desc_photo = $data->descricao;
        //Tratando a descrição NULL
        if($photos->desc_photo = ""){
            $photos->desc_photo = NULL;
        }
        //######################################
        $photos->link_photo = $data->link_foto;

        $fk = $photos->registerPhotograph();//Registrando no servidor

        $photo_to_save = $data->image;

        //manipulando imagem para salvar na pasta
        $photo_to_save = str_replace('data:image/png;base64,', '', $photo_to_save);
        $photo_to_save = str_replace(' ','+', $photo_to_save);
        $img = base64_decode($photo_to_save);
        $file = 'img/residents_photos/'.$photos->nome_photo;
        $success = file_put_contents($file, $img);
        //#######################################

        $mora = Container::getModel('Residents');
        foreach($mora->getAllResidentsRegisters() as $m){
            if($m['id_morador'] == $id_resident){
                $photos->updateFK($id_resident, $fk);//Parou aqui
            }
        }
    }
}
?>