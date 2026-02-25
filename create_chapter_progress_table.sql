-- Migration: Create chapter_progress table
-- Date: 2026-02-25
-- Purpose: Track student progress through course chapters

-- Create chapter_progress table
CREATE TABLE chapter_progress (
    id INT AUTO_INCREMENT NOT NULL,
    completed_at DATETIME DEFAULT NULL,
    quiz_score INT DEFAULT NULL,
    user_id INT NOT NULL,
    chapitre_id INT NOT NULL,
    INDEX IDX_C4189F43A76ED395 (user_id),
    INDEX IDX_C4189F431FBEEF7B (chapitre_id),
    UNIQUE INDEX user_chapter_unique (user_id, chapitre_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Add foreign key constraints
ALTER TABLE chapter_progress 
ADD CONSTRAINT FK_C4189F43A76ED395 
FOREIGN KEY (user_id) REFERENCES user (userId);

ALTER TABLE chapter_progress 
ADD CONSTRAINT FK_C4189F431FBEEF7B 
FOREIGN KEY (chapitre_id) REFERENCES chapitre (id);

-- Table description:
-- - id: Primary key
-- - user_id: Reference to the student (User entity)
-- - chapitre_id: Reference to the chapter (Chapitre entity)
-- - completed_at: Timestamp when the chapter was completed
-- - quiz_score: Score obtained in the chapter quiz
-- - user_chapter_unique: Ensures one progress record per user per chapter
