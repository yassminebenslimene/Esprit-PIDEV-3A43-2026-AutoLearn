-- Migration: Add workflow_status column to evenement table
-- Date: 2026-02-25
-- Purpose: Support Workflow Component for event state management

-- Add workflow_status column to evenement table
ALTER TABLE evenement ADD workflow_status VARCHAR(50) NOT NULL DEFAULT 'planifie';

-- Add workflow_status column to evenement_audit table (for audit tracking)
ALTER TABLE evenement_audit ADD workflow_status VARCHAR(50) DEFAULT NULL;

-- Update existing events with correct workflow_status based on current status
UPDATE evenement 
SET workflow_status = CASE 
    WHEN is_canceled = 1 THEN 'annule'
    WHEN status = 'EN_COURS' THEN 'en_cours'
    WHEN status = 'PASSE' THEN 'termine'
    ELSE 'planifie'
END;

-- Workflow states:
-- - planifie: Event is scheduled but not started yet
-- - en_cours: Event is currently running
-- - termine: Event has finished
-- - annule: Event has been cancelled
