-- CreateTable
CREATE TABLE `JudgeSubmission` (
    `id` VARCHAR(191) NOT NULL,
    `judgeId` VARCHAR(191) NOT NULL,
    `exhibitorId` VARCHAR(191) NOT NULL,
    `round` INTEGER NOT NULL DEFAULT 1,
    `submittedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    UNIQUE INDEX `JudgeSubmission_judgeId_exhibitorId_round_key`(`judgeId`, `exhibitorId`, `round`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `JudgeSubmission` ADD CONSTRAINT `JudgeSubmission_judgeId_fkey` FOREIGN KEY (`judgeId`) REFERENCES `Judge`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `JudgeSubmission` ADD CONSTRAINT `JudgeSubmission_exhibitorId_fkey` FOREIGN KEY (`exhibitorId`) REFERENCES `Exhibitor`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
