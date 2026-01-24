<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Administrative subdivision types used worldwide
 *
 * This enum represents the various types of administrative divisions
 * that countries use to organize their territory hierarchically.
 */
enum CountrySubdivisionType: int
{
    /** Administrative district in France, Belgium, Netherlands (e.g., Paris 1st Arrondissement) */
    case Arrondissement = 1;

    /** Self-governing region within a country (e.g., Catalonia in Spain, Greenland in Denmark) */
    case AutonomousRegion = 2;

    /** District within a city or urban area (e.g., Brooklyn in New York City, Camden in London) */
    case Borough = 3;

    /** Administrative division in Switzerland, France, Luxembourg (e.g., Canton of Zurich, Canton of Geneva) */
    case Canton = 4;

    /** Urban settlement or municipality (e.g., New York City, Tokyo, London) */
    case City = 5;

    /** Small administrative division in France, Belgium, Italy (e.g., Paris Commune, Brussels Commune) */
    case Commune = 6;

    /** Autonomous region in Spain (e.g., Valencian Community, Basque Community) */
    case Community = 7;

    /** Administrative division in USA, UK, Ireland (e.g., Los Angeles County, County Cork) */
    case County = 8;

    /** Administrative division in France, Colombia (e.g., Department of Paris, Antioquia Department) */
    case Department = 9;

    /** Administrative subdivision in many countries (e.g., Districts in India, Switzerland, Germany) */
    case District = 10;

    /** Administrative division in Bangladesh, India, Pakistan (e.g., Chittagong Division, Punjab Division) */
    case Division = 11;

    /** First-level division in United Arab Emirates (e.g., Dubai, Abu Dhabi, Sharjah) */
    case Emirate = 12;

    /** Administrative division in Middle East and North Africa (e.g., Cairo Governorate, Baghdad Governorate) */
    case Governorate = 13;

    /** State or federal state in Germany, Austria (e.g., Bavaria, Tyrol) */
    case Land = 14;

    /** Local government area in many countries (e.g., Oslo Municipality, Stockholm Municipality) */
    case Municipality = 15;

    /** Administrative division in Russia, Ukraine, Bulgaria (e.g., Moscow Oblast, Kyiv Oblast) */
    case Oblast = 16;

    /** Administrative division in Portugal, Louisiana USA (e.g., Lisbon Parish, Orleans Parish) */
    case Parish = 17;

    /** Administrative division in Japan, France, Greece (e.g., Tokyo Prefecture, Kyoto Prefecture) */
    case Prefecture = 18;

    /** First-level administrative division in many countries (e.g., Ontario in Canada, Sindh in Pakistan) */
    case Province = 19;

    /** Large administrative division in many countries (e.g., Tuscany in Italy, Île-de-France in France) */
    case Region = 20;

    /** First-level division in USA, India, Australia, etc. (e.g., California, Maharashtra, New South Wales) */
    case State = 21;

    /** Administrative division in Canada, Australia (e.g., Northwest Territories, Northern Territory) */
    case Territory = 22;

    /** Administrative division in Poland (e.g., Masovian Voivodeship, Lesser Poland Voivodeship) */
    case Voivodeship = 23;

    /** Administrative division in Japan, UK (e.g., Shibuya Ward in Tokyo, City of London Ward) */
    case Ward = 24;
}
