<?php
namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

class EventFormType extends EventAdminFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('recaptcha', EWZRecaptchaType::class, array(
            'label' => ' ',
            'constraints' => array(new Recaptcha\IsTrue)))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class,
        ));
    }
}
