<?php

namespace App\Controller\Assistant\Signatures;

use App\Model\Entity\Listas;

class DeleteSignature
{
    public static function deleteSignature($request, $type, $id)
    {
        switch ($type)
        {
            case "vai_volta":
                Listas\VaiVolta::getSignatureById($id)->excluir();
                break;

            case "saida":
                Listas\Saida::getSignatureById($id)->excluir();
                break;

            case "pernoite":
                Listas\Pernoite::getSignatureById($id)->excluir();
                break;
        }

        $request->getRouter()->redirect("/ass/listas");
    }
}

?>