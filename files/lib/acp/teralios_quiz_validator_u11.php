<?php

use wcf\system\WCF;

// names of old files.
$oldFiles = [
    'lib/system/quiz/validator/Validator.class.php',
    'lib/system/quiz/validator/ValidatorError.class.php',
    'lib/system/quiz/validator/data/AbstractDataHolder.class.php',
    'lib/system/quiz/validator/data/Goal.class.php',
    'lib/system/quiz/validator/data/IDataHolder.class.php',
    'lib/system/quiz/validator/data/IRawData.class.php',
    'lib/system/quiz/validator/data/Question.class.php',
    'lib/system/quiz/validator/data/Quiz.class.php',
    'lib/system/quiz/validator/data/Tag.class.php',
];

// get package id
$sql = 'SELECT  packageID
        FROM    ' . \wcf\data\package\Package::getDatabaseTableName() . '
        WHERE   packageName = ?';
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(['de.teralios.validator.quiz']);
$package = $statement->fetchArray();
$packageID = $package['packageID'];

// sql statement to delete old files.
$sql = 'DELETE
        FROM    wcf' . WCF_N . '_package_installation_file_log
        WHERE   packageID = ?
                AND filename LIKE ?';
$statement = WCF::getDB()->prepareStatement($sql);

// delete old files.
foreach ($oldFiles as $file) {
    $systemFile = WCF_DIR . $file;
    $fileRemove = unlink($systemFile);

    // if file is deleted => remove log entry.
    if ($fileRemove === true) {
        $statement->execute([$packageID, '%' . $file]);
    }
}
