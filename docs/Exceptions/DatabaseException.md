# DatabaseException

`\WebServCo\Framework\Exceptions\DatabaseException`

## Notes

* PDO:
    * Error code: Use MySQL error number instead of SQLState;
    * Error message: simplified message without the codes;
        * Complete error message can be accessed via the previous exception;
* PDO, MYSQLi
    * The SQLState can be accessed using the method `getSqlState()`;
