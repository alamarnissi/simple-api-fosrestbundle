<?php
    namespace App\Doctrine;

    use App\Entity\Utilisateur;
    use Doctrine\Common\EventSubscriber;
    use Doctrine\ORM\Event\LifecycleEventArgs;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    class HashPasswordListener implements EventSubscriber
    {
        private $passwordEncoder;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder)
        {
            $this->passwordEncoder = $passwordEncoder;
        }

        /**
         * @inheritDoc
         */public function getSubscribedEvents()
        {
            return ['prePersist', 'preUpdate'];
        }

        public function prePersist(LifecycleEventArgs $args)
        {
            $entity = $args->getEntity();
            if (!$entity instanceof Utilisateur)
            {
                return;
            }

            $this->encodePassword($entity);
        }

        public function preUpdate(LifecycleEventArgs $args)
        {
            $entity = $args->getEntity();
            if (!$entity instanceof Utilisateur) {
                return;
            }
            $this->encodePassword($entity);
            // necessary to force the update to see the change
            $em = $args->getEntityManager();
            $meta = $em->getClassMetadata(get_class($entity));
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
        }

        /**
         * @param Utilisateur $entity
         */
        private function encodePassword(Utilisateur $entity)
        {
            if (!$entity->getPlainPassword()) {
                return;
            }
            $encoded = $this->passwordEncoder->encodePassword(
                $entity,
                $entity->getPlainPassword()
            );
            $entity->setPassword($encoded);
        }
    }