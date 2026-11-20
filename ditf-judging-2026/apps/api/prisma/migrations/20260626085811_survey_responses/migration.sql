-- CreateTable
CREATE TABLE `SurveyResponse` (
    `id` VARCHAR(191) NOT NULL,
    `companyName` VARCHAR(191) NOT NULL,
    `participant` VARCHAR(191) NOT NULL,
    `phone` VARCHAR(191) NULL,
    `surveyDate` VARCHAR(191) NULL,
    `q1` VARCHAR(191) NULL,
    `q2` VARCHAR(191) NULL,
    `q3` VARCHAR(191) NULL,
    `q4` TEXT NOT NULL DEFAULT '[]',
    `q5` TEXT NULL,
    `q6` TEXT NULL,
    `q7` TEXT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
