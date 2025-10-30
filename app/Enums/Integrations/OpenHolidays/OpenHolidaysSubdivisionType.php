<?php

declare(strict_types=1);

namespace App\Enums\Integrations\OpenHolidays;

use App\Enums\PeopleDear\CountrySubdivisionType;

/**
 * OpenHolidays API subdivision type mapping
 *
 * Maps subdivision category strings from OpenHolidays API
 * to internal CountrySubdivisionType enum values.
 *
 * Based on analysis of API fixtures for Portugal and Spain.
 */
enum OpenHolidaysSubdivisionType: string
{
    /** Portugal: distrito (e.g., Aveiro, Braga) */
    case District = 'distrito';

    /** Portugal: município (e.g., Águeda, Lisboa) */
    case Municipality = 'município';

    /** Portugal: região autónoma (e.g., Região Autónoma dos Açores) */
    case AutonomousRegion = 'região autónoma';

    /** Spain: provincia (e.g., Almería, Cádiz, Barcelona) */
    case Province = 'provincia';

    /** Spain: Comunidad autónoma (e.g., Andalucía, Cataluña) */
    case AutonomousCommunity = 'Comunidad autónoma';

    /** Spain: Ciudad autónoma del norte de África (e.g., Ceuta, Melilla) */
    case AutonomousCity = 'Ciudad autónoma del norte de África';

    /** Spain: Comunidad de Madrid (specific case for Madrid community) */
    case Community = 'Comunidad de Madrid';

    /**
     * Transform OpenHolidays subdivision type to internal CountrySubdivisionType
     *
     * Maps API category strings to standardized subdivision types.
     * Defaults to District for any unmapped types.
     */
    public function transform(): CountrySubdivisionType
    {
        return match ($this) {
            self::District => CountrySubdivisionType::District,
            self::Municipality => CountrySubdivisionType::Municipality,
            self::AutonomousRegion, self::AutonomousCommunity => CountrySubdivisionType::AutonomousRegion,
            self::Province => CountrySubdivisionType::Province,
            self::AutonomousCity => CountrySubdivisionType::City,
            self::Community => CountrySubdivisionType::Community,
        };
    }
}
