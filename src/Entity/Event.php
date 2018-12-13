<?php

namespace App\Entity;

use App\Form\MixDesk;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @UniqueEntity("token")
 */
class Event extends News
{
    const STATUS_APPROVED = 'approved';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';

    const NEWS_TYPE = 'event';

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank()
     */
    private $region;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceMax;

    /**
     * @ORM\ManyToOne(targetEntity="EventType", inversedBy="events")     *
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subgenre", inversedBy="events")
     * @Assert\NotBlank()
     */
    private $subgenre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Settlement", inversedBy="events")
     */
    private $settlement;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Weapon", inversedBy="events")
     */
    private $weapons;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $organizers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $organizerContact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $contactSite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex ("'https:\/\/www\.facebook\.com\/.*'", message="Адрес должен начинаться с 'https://www.facebook.com/'")
     */
    private $contactFB;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex ("'https:\/\/vk\.com\/.*'", message="Адрес должен начинаться с 'https://vk.com/'")
     *
     */
    private $contactVK;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex ("'https:\/\/t\.me\/.*'", message="Адрес должен начинаться с 'https://t.me/'")
     */
    private $contactTelegram;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contactOther;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskRuntimeGM;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskOpenness;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixdeskPlayerPressure;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskCharCreation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskMetatechniques;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskStoryEngine;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskCommunicationStyle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskBleedIn;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskLoyaltyToSetting;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskRepresentaionOfTheme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mixDeskScenography;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public $recaptcha;

    public function __construct()
    {
        $this->weapons = new ArrayCollection();
    }


    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getPriceMin(): ?int
    {
        return $this->priceMin;
    }

    public function setPriceMin(?int $priceMin): self
    {
        $this->priceMin = $priceMin;

        return $this;
    }

    public function getPriceMax(): ?int
    {
        return $this->priceMax;
    }

    public function setPriceMax(?int $priceMax): self
    {
        $this->priceMax = $priceMax;

        return $this;
    }

    public function getOrganizers(): ?string
    {
        return $this->organizers;
    }

    public function setOrganizers(string $organizers): self
    {
        $this->organizers = $organizers;

        return $this;
    }

    public function getOrganizerContact(): ?string
    {
        return $this->organizerContact;
    }

    public function setOrganizerContact(string $organizerContact): self
    {
        $this->organizerContact = $organizerContact;

        return $this;
    }

    public function getContactSite(): ?string
    {
        return $this->contactSite;
    }

    public function setContactSite(?string $contactSite): self
    {
        $this->contactSite = $contactSite;

        return $this;
    }

    public function getContactFB(): ?string
    {
        return $this->contactFB;
    }

    public function setContactFB(?string $contactFB): self
    {
        $this->contactFB = $contactFB;

        return $this;
    }

    public function getContactOther(): ?string
    {
        return $this->contactOther;
    }

    public function setContactOther(?string $contactOther): self
    {
        $this->contactOther = $contactOther;

        return $this;
    }

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(?EventType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSubgenre(): ?Subgenre
    {
        return $this->subgenre;
    }

    public function setSubgenre($subgenre): self
    {
        $this->subgenre = $subgenre;

        return $this;
    }

    public function getSettlement(): ?Settlement
    {
        return $this->settlement;
    }

    public function setSettlement(?Settlement $settlement): self
    {
        $this->settlement = $settlement;

        return $this;
    }

    /**
     * @return Collection|Weapon[]
     */
    public function getWeapons(): Collection
    {
        return $this->weapons;
    }

    public function addWeapon(Weapon $weapon): self
    {
        if (!$this->weapons->contains($weapon)) {
            $this->weapons[] = $weapon;
        }

        return $this;
    }

    public function removeWeapon(Weapon $weapon): self
    {
        if ($this->weapons->contains($weapon)) {
            $this->weapons->removeElement($weapon);
        }

        return $this;
    }

    public function getContactVK(): ?string
    {
        return $this->contactVK;
    }

    public function setContactVK(?string $contactVK): self
    {
        $this->contactVK = $contactVK;

        return $this;
    }

    public function getContactTelegram(): ?string
    {
        return $this->contactTelegram;
    }

    public function setContactTelegram(?string $contactTelegram): self
    {
        $this->contactTelegram = $contactTelegram;

        return $this;
    }

    public function getMixDeskRuntimeGM(): ?int
    {
        return $this->mixDeskRuntimeGM;
    }

    public function setMixDeskRuntimeGM(?int $mixDeskRuntimeGM): self
    {
        $this->mixDeskRuntimeGM = $mixDeskRuntimeGM;

        return $this;
    }

    public function getMixDeskOpenness(): ?int
    {
        return $this->mixDeskOpenness;
    }

    public function setMixDeskOpenness(?int $mixDeskOpenness): self
    {
        $this->mixDeskOpenness = $mixDeskOpenness;

        return $this;
    }

    public function getMixdeskPlayerPressure(): ?int
    {
        return $this->mixdeskPlayerPressure;
    }

    public function setMixdeskPlayerPressure(?int $mixdeskPlayerPressure): self
    {
        $this->mixdeskPlayerPressure = $mixdeskPlayerPressure;

        return $this;
    }

    public function getMixDeskCharCreation(): ?int
    {
        return $this->mixDeskCharCreation;
    }

    public function setMixDeskCharCreation(?int $mixDeskCharCreation): self
    {
        $this->mixDeskCharCreation = $mixDeskCharCreation;

        return $this;
    }

    public function getMixDeskMetatechniques(): ?int
    {
        return $this->mixDeskMetatechniques;
    }

    public function setMixDeskMetatechniques(?int $mixDeskMetatechniques): self
    {
        $this->mixDeskMetatechniques = $mixDeskMetatechniques;

        return $this;
    }

    public function getMixDeskStoryEngine(): ?int
    {
        return $this->mixDeskStoryEngine;
    }

    public function setMixDeskStoryEngine(?int $mixDeskStoryEngine): self
    {
        $this->mixDeskStoryEngine = $mixDeskStoryEngine;

        return $this;
    }

    public function getMixDeskCommunicationStyle(): ?int
    {
        return $this->mixDeskCommunicationStyle;
    }

    public function setMixDeskCommunicationStyle(?int $mixDeskCommunicationStyle): self
    {
        $this->mixDeskCommunicationStyle = $mixDeskCommunicationStyle;

        return $this;
    }

    public function getMixDeskBleedIn(): ?int
    {
        return $this->mixDeskBleedIn;
    }

    public function setMixDeskBleedIn(?int $mixDeskBleedIn): self
    {
        $this->mixDeskBleedIn = $mixDeskBleedIn;

        return $this;
    }

    public function getMixDeskLoyaltyToSetting(): ?int
    {
        return $this->mixDeskLoyaltyToSetting;
    }

    public function setMixDeskLoyaltyToSetting(?int $mixDeskLoyaltyToSetting): self
    {
        $this->mixDeskLoyaltyToSetting = $mixDeskLoyaltyToSetting;

        return $this;
    }

    public function getMixDeskRepresentaionOfTheme(): ?int
    {
        return $this->mixDeskRepresentaionOfTheme;
    }

    public function setMixDeskRepresentaionOfTheme(?int $mixDeskRepresentaionOfTheme): self
    {
        $this->mixDeskRepresentaionOfTheme = $mixDeskRepresentaionOfTheme;

        return $this;
    }

    public function getMixDeskScenography(): ?int
    {
        return $this->mixDeskScenography;
    }

    public function setMixDeskScenography(?int $mixDeskScenography): self
    {
        $this->mixDeskScenography = $mixDeskScenography;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @Assert\IsTrue(message="Пожалуйста, проверьте указанные даты: событие не может закончиться до своего начала.")
     */
    public function isEndDateSane()
    {
        return $this->getStartDate() <= $this->getEndDate();
    }

    /**
     * @return bool
     */
    public function isMixDesk()
    {
        return ($this->getMixDeskRuntimeGM() && $this->getMixDeskOpenness()&& $this->getMixDeskBleedIn() && $this->getMixDeskCharCreation() && $this->getMixDeskCommunicationStyle() && $this->getMixDeskLoyaltyToSetting() && $this->getMixDeskMetatechniques() && $this->getMixdeskPlayerPressure() && $this->getMixDeskRepresentaionOfTheme() && $this->getMixDeskScenography() && $this->getMixDeskStoryEngine());
    }

    /**
     * @Assert\IsTrue(message="Пожалуйста, укажите все параметры эквалайзера.")
     */
    public function isMixDeskValid()
    {
        if ($this->getMixDeskRuntimeGM() || $this->getMixDeskOpenness()|| $this->getMixDeskBleedIn() || $this->getMixDeskCharCreation() || $this->getMixDeskCommunicationStyle() || $this->getMixDeskLoyaltyToSetting() || $this->getMixDeskMetatechniques() || $this->getMixdeskPlayerPressure() || $this->getMixDeskRepresentaionOfTheme() || $this->getMixDeskScenography() || $this->getMixDeskStoryEngine()) {
            return ($this->isMixDesk());
        } else {
            return true;
        }
    }

    /**
     * @return string
     */
    public function createGoogleLocation()
    {
        if ($this->location) {
            return sprintf('%s, %s', $this->getLocation(), $this->getRegion()->getName());
        } else {
            return $this->getRegion()->getName();
        }
    }

    /**
     * @return string
     */
    public function createGoogleDescription()
    {
        $googleDescription =
            'Формат: ' . $this->getType()->getName() . PHP_EOL .
            'Жанр: ' . $this->getSubgenre()->getGenre()->getName() . PHP_EOL .
            'Поджанр: ' . $this->getSubgenre()->getName() . PHP_EOL . PHP_EOL .
            'Организаторы: ' . $this->getOrganizers() . PHP_EOL. PHP_EOL;

        if ($this->settlement) {
            $googleDescription = $googleDescription .'Тип поселения: ' . $this->getSettlement()->getName() . PHP_EOL. PHP_EOL;
        }

        if (count($this->weapons) >0) {
            $weaponCollection = [];
            foreach ($this->weapons as $weapon) {
                $weaponString [] = $weapon->getName();
            }
            $googleDescription = $googleDescription .'Материал оружия: ' . implode(", ", $weaponCollection) . PHP_EOL. PHP_EOL;
        }

        if ($this->priceMin || $this->priceMax) {
            $googleDescription = $googleDescription .'Взнос: ';
            if ($this->priceMin) {
                $googleDescription = $googleDescription . $this->getPriceMin();

                if ($this->priceMax && $this->priceMax!= $this->priceMin) {
                    $googleDescription = $googleDescription . ' - ' .$this->getPriceMax();
                }
            } else {
                $googleDescription = $googleDescription . $this->getPriceMax();
            }
            $googleDescription = $googleDescription . 'грн.' . PHP_EOL . PHP_EOL;
        }

        if ($this->contactSite || $this->contactFB || $this->contactVK || $this->contactTelegram || $this->contactOther) {
            $googleDescription = $googleDescription .'Ссылки: '. PHP_EOL;
            if ($this->contactSite) {
                $googleDescription = $googleDescription . 'Сайт: ' . $this->contactSite . PHP_EOL;
            }
            if ($this->contactFB) {
                $googleDescription = $googleDescription . 'Facebook: ' . $this->contactFB . PHP_EOL;
            }
            if ($this->contactVK) {
                $googleDescription = $googleDescription . 'Вконтакте: ' . $this->contactVK . PHP_EOL;
            }
            if ($this->contactTelegram) {
                $googleDescription = $googleDescription . 'Телеграм: ' . $this->contactTelegram . PHP_EOL;
            }
            if ($this->contactOther) {
                $googleDescription = $googleDescription . $this->contactOther . PHP_EOL;
            }
        }

        $googleDescription = $googleDescription . PHP_EOL . 'Описание: ' . PHP_EOL . $this->getDescription();

        return $googleDescription;
    }

    /**
     * @return array
     */
    public function getMixDeskDescription()
    {
        $description[] = substr(array_search($this->getMixDeskRuntimeGM(), MixDesk::RuntimeGM['values']), 3);
        $description[] = substr(array_search($this->getMixDeskOpenness(), MixDesk::Openness['values']), 3);
        $description[] = substr(array_search($this->getMixdeskPlayerPressure(), MixDesk::PlayerPressure['values']), 3);
        $description[] = substr(array_search($this->getMixDeskCharCreation(), MixDesk::CharCreation['values']), 3);
        $description[] = substr(array_search($this->getMixDeskMetatechniques(), MixDesk::Metatechniques['values']), 3);
        $description[] = substr(array_search($this->getMixDeskStoryEngine(), MixDesk::StoryEngine['values']), 3);
        $description[] = substr(array_search($this->getMixDeskCommunicationStyle(), MixDesk::CommunicationStyle['values']), 3);
        $description[] = substr(array_search($this->getMixDeskBleedIn(), MixDesk::BleedIn['values']), 3);
        $description[] = substr(array_search($this->getMixDeskLoyaltyToSetting(), MixDesk::LoyaltyToSetting['values']), 3);
        $description[] = substr(array_search($this->getMixDeskRepresentaionOfTheme(), MixDesk::RepresentaionOfTheme['values']), 3);
        $description[] = substr(array_search($this->getMixDeskScenography(), MixDesk::Scenography['values']), 3);
        return $description;
    }
}
