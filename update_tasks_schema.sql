-- Add task_name field and rename description to notes
ALTER TABLE work_order_tasks 
ADD COLUMN task_name VARCHAR(255) NOT NULL AFTER work_order_id,
CHANGE COLUMN description notes TEXT;

-- Update existing tasks to use description as task_name
UPDATE work_order_tasks SET task_name = LEFT(notes, 100) WHERE task_name = '';