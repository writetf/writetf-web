<?php

namespace App\Form\Type;

use App\Entity\Community;
use App\Repository\CommunityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChannelSelectType extends AbstractType
{
    private $channels;

    public function __construct(CommunityRepository $channels)
    {
        $this->channels = $channels;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->channels->findAll(),
            'choice_label' => function (Community $channel) {
                return $channel->getDisplayText();
            },
            'attr' => [
                'class' => 'selectpicker border',
                'data-live-search' => true,
                'data-style' => 'btn-default'
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
