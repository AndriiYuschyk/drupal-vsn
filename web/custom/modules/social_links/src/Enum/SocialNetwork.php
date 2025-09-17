<?php

namespace Drupal\social_links\Enum;

/**
 * Enum for supported social networks.
 */
enum SocialNetwork: string {
    case Instagram = 'instagram';
    case Facebook = 'facebook';
    case Telegram = 'telegram';
    case Twitter = 'twitter';
    case Discord = 'discord';
    case TikTok = 'tiktok';
    case WhatsApp = 'whatsapp';
    case YouTube = 'youtube';
    case LinkedIn = 'linkedin';

    /**
     * Human-readable label for each network.
     */
    public function label(): string {
        return match($this) {
            SocialNetwork::Instagram => 'Instagram',
            SocialNetwork::Facebook  => 'Facebook',
            SocialNetwork::Telegram  => 'Telegram',
            SocialNetwork::Twitter   => 'Twitter (X)',
            SocialNetwork::Discord   => 'Discord',
            SocialNetwork::TikTok    => 'TikTok',
            SocialNetwork::WhatsApp  => 'WhatsApp',
            SocialNetwork::YouTube   => 'YouTube',
            SocialNetwork::LinkedIn  => 'LinkedIn',
        };
    }

    /**
     * Default weight (order).
     */
    public function defaultWeight(): int {
        return match($this) {
            SocialNetwork::Instagram => 0,
            SocialNetwork::Facebook  => 1,
            SocialNetwork::Telegram  => 2,
            SocialNetwork::Twitter   => 3,
            SocialNetwork::Discord   => 4,
            SocialNetwork::TikTok    => 5,
            SocialNetwork::WhatsApp  => 6,
            SocialNetwork::YouTube   => 7,
            SocialNetwork::LinkedIn  => 8,
        };
    }

    /**
     * Return all networks as array.
     *
     * @return SocialNetwork[]
     */
    public static function all(): array {
        return [
            self::Instagram,
            self::Facebook,
            self::Telegram,
            self::Twitter,
            self::Discord,
            self::TikTok,
            self::WhatsApp,
            self::YouTube,
            self::LinkedIn,
        ];
    }
}