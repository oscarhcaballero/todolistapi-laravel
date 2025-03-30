<?php
namespace App\Exceptions;

use Exception;

/**
 * Class TaskException
 *
 * This custom exception class is used to handle errors related to task operations.
 * It provides static methods to create specific exceptions for task creation,
 * updating, and deletion failures, ensuring consistency in error handling.
 */
class TaskException extends Exception
{
   

    /**
     * Create a new exception for task creation failure.
     *
     * @param string $message A message describing the error.
     * @param mixed $data The data that caused the failure.
     * @return TaskException The created exception instance.
     */
    public static function createFailed($message, $data): TaskException
    {
        $dataString = json_encode($data);  
        return new self("Error creating task: $message. Data: $dataString");
    }

    /**
     * Create a new exception for task update failure.
     *
     * @param string $message A message describing the error.
     * @param mixed $taskId The ID of the task that failed to update.
     * @param mixed $data The data that caused the failure.
     * @return TaskException The created exception instance.
     */
    public static function updateFailed($message, $taskId, $data): TaskException
    {
        $dataString = json_encode($data);
        return new self("Error updating task (ID: $taskId): $message. Data: $dataString");
    }

    /**
     * Create a new exception for task deletion failure.
     *
     * @param string $message A message describing the error.
     * @param mixed $taskId The ID of the task that failed to delete.
     * @return TaskException The created exception instance.
     */
    public static function deleteFailed($message, $taskId): TaskException
    {
        return new self("Error deleting task (ID: $taskId): $message", 0, null);
    }
}