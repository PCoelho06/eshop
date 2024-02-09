<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user')->onlyOnIndex()->formatValue(function ($value, $comment) {
                return $comment->getUser()->getFullName() . ' ' . $comment->getUser()->getEmail();
            }),
            TextareaField::new('content')->setLabel('Commentaires')->stripTags()->setMaxLength(300),
            IntegerField::new('rating')->setLabel('Vote'),
            DateField::new('createdAt')->setLabel('Date'),

        ];
    }
   
}
