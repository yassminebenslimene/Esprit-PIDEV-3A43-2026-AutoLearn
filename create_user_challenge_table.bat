@echo off
echo Creating user_challenge table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS user_challenge (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, challenge_id INT NOT NULL, current_index INT DEFAULT NULL, answers JSON DEFAULT NULL, completed_at DATETIME DEFAULT NULL, score INT DEFAULT NULL, total_points INT DEFAULT NULL, started_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_user_challenge_user (user_id), INDEX IDX_user_challenge_challenge (challenge_id), UNIQUE INDEX UNIQ_user_challenge_user_challenge (user_id, challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"

echo Adding foreign key constraints...
php bin/console doctrine:query:sql "ALTER TABLE user_challenge ADD CONSTRAINT FK_user_challenge_user FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE"
php bin/console doctrine:query:sql "ALTER TABLE user_challenge ADD CONSTRAINT FK_user_challenge_challenge FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE"

echo Done!
pause
