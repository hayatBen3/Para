<?php

namespace App\Controller\Admin;

use App\Entity\Fournisseur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FournisseurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fournisseur::class;
    }

    
    // public function configureFields(string $pageName): iterable
    // {
    //     return [
           
    //         TextField::new('name'),
    //         TextField::new('email'),
    //         TextField::new('name'),
    //         TextField::new('adresse'),
    //         TextField::new('phone'),
            
            
            

    //     ];
    // }
    
}
