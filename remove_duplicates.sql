-- Remove duplicate asset categories
USE supporttracker;

DELETE t1 FROM asset_categories t1
INNER JOIN asset_categories t2 
WHERE t1.id > t2.id AND t1.name = t2.name;