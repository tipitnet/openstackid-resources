<?php namespace Database\Migrations\Model;
/**
 * Copyright 2019 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use LaravelDoctrine\Migrations\Schema\Table;
use LaravelDoctrine\Migrations\Schema\Builder;
/**
 * Class Version20190529142913
 * @package Database\Migrations\Model
 */
class Version20190529142913 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if(!$schema->hasTable("SummitBookableVenueRoom")) {
            $sql = <<<SQL
ALTER TABLE SummitAbstractLocation MODIFY ClassName enum('SummitAbstractLocation', 'SummitGeoLocatedLocation', 'SummitExternalLocation', 'SummitAirport', 'SummitHotel', 'SummitVenue', 'SummitVenueRoom', 'SummitBookableVenueRoom') DEFAULT 'SummitAbstractLocation';
SQL;
            $builder = new Builder($schema);
            $this->addSql($sql);
            $builder->create('SummitBookableVenueRoom', function (Table $table) {
                $table->integer("ID", true, false);
                $table->primary("ID");
                $table->string("Currency",3);
                $table->decimal("TimeSlotCost", 9, 2)->setDefault('0.00');
                $table->foreign("SummitAbstractLocation","ID", "ID", ["onDelete" => "CASCADE"]);
            });

            $this->addSql($sql);

        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
ALTER TABLE SummitAbstractLocation MODIFY ClassName enum('SummitAbstractLocation', 'SummitGeoLocatedLocation', 'SummitExternalLocation', 'SummitAirport', 'SummitHotel', 'SummitVenue', 'SummitVenueRoom') DEFAULT 'SummitAbstractLocation';
SQL;
        $builder = new Builder($schema);
        $this->addSql($sql);

        $builder->drop('SummitBookableVenueRoom');
    }
}
