<?php

declare(strict_types=1);

namespace Models\VisualConsole\Items;
use Models\Model;

final class Line extends Model
{


    /**
     * Validate the input data
     *
     * @param mixed $data
     *
     * @return void
     */
    protected function validateData(array $data): void
    {
        if (isset($data['id']) === false
            || \is_numeric($data['id']) === false
        ) {
            throw new \InvalidArgumentException(
                'the Id property is required and should be integer'
            );
        }

        if (isset($data['type']) === false
            || \is_numeric($data['type']) === false
        ) {
            throw new \InvalidArgumentException(
                'the Id property is required and should be integer'
            );
        }
    }


    /**
     * Returns a valid data structure.
     *
     * @param mixed $data
     *
     * @return array
     */
    protected function decode(array $data): array
    {
        return [
            'id'          => (int) $data['id'],
            'type'        => LINE_ITEM,
            'startX'      => $this->extractStartX($data),
            'startY'      => $this->extractStartY($data),
            'endX'        => $this->extractEndX($data),
            'endY'        => $this->extractEndY($data),
            'isOnTop'     => $this->extractIsOnTope($data),
            'borderWidth' => $this->extractBorderWidth($data),
            'borderColor' => $this->extractBorderColor($data),
        ];
    }


    /**
     * Extract the value of startX and
     * return a integer.
     *
     * @param mixed $data
     *
     * @return integer
     */
    private function extractStartX(array $data): int
    {
        $startX = Model::parseIntOr(
            Model::issetInArray($data, ['startX', 'pos_x']),
            0
        );
        return $startX;
    }


    /**
     * Extract the value of startY and
     * return a integer.
     *
     * @param mixed $data
     *
     * @return integer
     */
    private function extractStartY(array $data): int
    {
        $startY = Model::parseIntOr(
            Model::issetInArray($data, ['startY', 'pos_y']),
            0
        );
        return $startY;
    }


    /**
     * Extract the value of endX and
     * return a integer.
     *
     * @param mixed $data
     *
     * @return integer
     */
    private function extractEndX(array $data): int
    {
        $endX = Model::parseIntOr(
            Model::issetInArray($data, ['endX', 'width']),
            0
        );
        return $endX;
    }


    /**
     * Extract the value of endY and
     * return a integer.
     *
     * @param mixed $data
     *
     * @return integer
     */
    private function extractEndY(array $data): int
    {
        $endY = Model::parseIntOr(
            Model::issetInArray($data, ['endY', 'height']),
            0
        );
        return $endY;
    }


    /**
     * Extract the value of isOnTop and
     * return a bool.
     *
     * @param mixed $data
     *
     * @return boolean
     */
    private function extractIsOnTope(array $data): bool
    {
        $isOnTop = Model::parseBool(
            Model::issetInArray($data, ['isOnTop', 'show_on_top'])
        );
        return $isOnTop;
    }


    /**
     * Extract the value of borderWidth and
     * return a integer.
     *
     * @param mixed $data
     *
     * @return integer
     */
    private function extractBorderWidth(array $data): int
    {
        $borderWidth = Model::parseIntOr(
            Model::issetInArray($data, ['borderWidth', 'border_width']),
            0
        );
        if ($borderWidth >= 0) {
            return $borderWidth;
        } else {
            return 0;
        }
    }


    /**
     * Extract the value of borderColor and
     * return to not empty string or null.
     *
     * @param mixed $data
     *
     * @return void
     */
    private function extractBorderColor(array $data)
    {
        $borderColor = Model::notEmptyStringOr(
            Model::issetInArray($data, ['borderColor', 'border_color']),
            null
        );
        return $borderColor;
    }


    /**
     * Obtain a vc item data structure from the database using a filter.
     *
     * @param array $filter Filter of the Visual Console Item.
     *
     * @return array The Visual Console line data structure stored into the DB.
     * @throws \Exception When the data cannot be retrieved from the DB.
     *
     * @override Model::fetchDataFromDB.
     */
    protected static function fetchDataFromDB(array $filter): array
    {
        // Due to this DB call, this function cannot be unit tested without
        // a proper mock.
        $row = \db_get_row_filter('tlayout_data', $filter);

        if ($row === false) {
            throw new \Exception('error fetching the data from the DB');
        }

        return $row;
    }


}
