<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class IftttHandlerTelegram
{
    private $key;
    private $logger;
    private $request;

    public function __construct($key, LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->key = $key;
        $this->logger = $logger;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Event) {
            return;
        }

        $changeSet = $args->getEntityChangeSet();
        if (key_exists('status', $changeSet)) {
            if ($changeSet['status'][0] == Event::STATUS_PENDING && $changeSet['status'][1] == Event::STATUS_APPROVED) {
                $this->handleEventAnnouncement($entity);
            }
        }
    }

    public function handleEventAnnouncement(Event $event)
    {
        $this->logger->info('Calling IFTTT...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        $url = sprintf(
            "https://maker.ifttt.com/trigger/%s/with/key/%s",
            'event_approved',
            $this->key
        );
        $this->logger->info(sprintf('Calling URL: %s', $url));
        curl_setopt($ch, CURLOPT_URL, $url);
        $imgUrl = $this->generateImageUrl($event);
        $body = json_encode(
            [
                "value1" => nl2br($this->createEventAnnouncement($event)),
                "value2" => $imgUrl],
            JSON_UNESCAPED_UNICODE
        );
        $this->logger->info(sprintf('Request body: %s', $body));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        $this->logger->info(sprintf('Got response: %s', $response));
        curl_close($ch);
    }

    public function createEventAnnouncement(Event $event)
    {
        $announcement =
            sprintf("В Ролендарь добавлено новое событие: %s '%s'",
                mb_strtolower($event->getType()->getName()),
                $event->getName());

        $announcement = $announcement . PHP_EOL . 'Организаторы: ' . $event->getOrganizers() . PHP_EOL;

        if ($event->getStartDate()->format('Y-m-d') != $event->getEndDate()->format('Y-m-d')) {
            $announcement = $announcement .'Дата: ' . $event->getStartDate()->format('Y-m-d') . ' - ' . $event->getEndDate()->format('Y-m-d')  . PHP_EOL;
        } else {
            $announcement = $announcement .'Дата: ' . $event->getStartDate()->format('Y-m-d') . PHP_EOL;
        }

        if ($event->getLocation()) {
            $announcement = $announcement .'Место проведения: ' . $event->getLocation() . ', ' . $event->getRegion()->getName()  . PHP_EOL;
        } else {
            if ($event->getRegion()) {
                $announcement = $announcement .'Место проведения: ' . $event->getRegion()->getName()  . PHP_EOL;
            }
        }

        if ($event->getSettlement()) {
            $announcement = $announcement .'Тип поселения: ' . mb_strtolower($event->getSettlement()->getName()) . PHP_EOL;
        }

        if (count($event->getWeapons()) >0) {
            $weaponCollection = [];
            foreach ($event->getWeapons() as $weapon) {
                $weaponCollection [] = $weapon->getName();
            }
            $announcement = $announcement .'Материал оружия: ' . implode(", ", $weaponCollection) . PHP_EOL;
        }

        if ($event->getPriceMin() || $event->getPriceMax()) {
            $announcement = $announcement .'Взнос: ';
            if ($event->getPriceMin()) {
                $announcement = $announcement . $event->getPriceMin();

                if ($event->getPriceMax() && $event->getPriceMax()!= $event->getPriceMin()) {
                    $announcement = $announcement . ' - ' .$event->getPriceMax();
                }
            } else {
                $announcement = $announcement . $event->getPriceMax();
            }
            $announcement = $announcement . 'грн.' . PHP_EOL;
        }

        if ($event->getContactSite() || $event->getContactFB() || $event->getContactVK() || $event->getContactTelegram() || $event->getContactOther()) {
            $announcement = $announcement .'Ссылки: '. PHP_EOL;
            if ($event->getContactSite()) {
                $announcement = $announcement . 'Сайт: ' . $event->getContactSite() . PHP_EOL;
            }
            if ($event->getContactFB()) {
                $announcement = $announcement . 'Facebook: ' . $event->getContactFB() . PHP_EOL;
            }
            if ($event->getContactVK()) {
                $announcement = $announcement . 'Вконтакте: ' . $event->getContactVK() . PHP_EOL;
            }
            if ($event->getContactTelegram()) {
                $announcement = $announcement . 'Телеграм: ' . $event->getContactTelegram() . PHP_EOL;
            }
            if ($event->getContactOther()) {
                $announcement = $announcement . $event->getContactOther() . PHP_EOL;
            }
        }

        $announcement = $announcement . PHP_EOL . $event->getDescription();

        return $announcement;
    }

    public function generateImageUrl(Event $event)
    {
        if ($event->getPicture()) {
            $imgUrl = $this->request->headers->get('host') . '/images/pictures/' . $event->getPicture();
        } else {
            $imgUrl = $this->request->headers->get('host') . '/images/rolendar-1.jpg';
        }
        return $imgUrl;
    }
}
