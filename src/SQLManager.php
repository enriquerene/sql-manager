<?php


namespace RESTfulTemplate;

use Exception;
use PDO;

class SQLManager extends PDO {
	const REQUIRED_FIELDS = [ "host", "name", "charset", "user", "password", "prefix", "tables" ];
	const DEFAULT_PDO_OPTIONS = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];

    private $tableStructure;
    private $host;
    private $name;
    private $charset;
    private $prefix;
    private $debug = false;
    private $whereJoinner = "AND";
    private $whereComparative = "="; // =, LIKE
    private $whereType = "normal"; // like, interval, csv
    private $selectLimit = null;
    private $selectOffset = null;

	function __construct ( array $dbInfo, array $pdoOptions = [] )
	{
		$missingFields = [];
		$dbInfoKeys = array_keys( $dbInfo );
		foreach ( $this->REQUIRED_FIELDS as $fieldName )
		{
			if ( ! in_array( $fieldName, $dbInfoKeys ) )
			{
				$missingFields[] = $fieldName;
			}
		}
		if ( count( $missingFields ) > 0 ) 
			throw new Exception ( "SQLManager instance missing " . implode( ", ", $missingFields ) . " fields in construct array argument. See https://github.com/enriquerene/sql-manager#usage for help." );
		unset( $missingFields, $dbInfoKeys, $fieldName );

        $dsn = "mysql:host=" . $dbInfo[ "host" ] . ";dbname=" . $dbInfo[ "name" ] . ";charset=" . $dbInfo[ "charset" ];
		// developer note for future version:
		// think about how to handle $pdoOptions from args
		$options = $this->DEFAULT_PDO_OPTIONS;
        parent::__construct( $dsn, $dbInfo[ "user" ], $dbInfo[ "password" ], $options );
        $this->setTableStructure( $dbInfo[ "tables" ] );
        $this->setHost( $dbInfo[ "host" ] );
        $this->setName( $dbInfo[ "name" ] );
        $this->setCharset( $dbInfo[ "charset" ] );
        $this->setPrefix( $dbInfo[ "prefix" ] );
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function setTableStructure ( array $tableStructure ): void
   	{
        $this->tableStructure = $tableStructure;
    }

    /**
	 * Get table structure from object state.
	 *
     * @return tableStructure array
     */
	private function getTableStructure (): void
   	{
        return $this->tableStructure;
    }
	
    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function setName ( string $name ): void
	{
        $this->name = $name;
	}

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function setHost ( string $host ): void
	{
        $this->host = $host;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function setCharset ( string $charset ): void
	{
        $this->charset = $charset;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function setPrefix ( string $prefix ): void
	{
        $this->prefix = $prefix;
	}

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	private function quoteFields ( string $tableName, array $fields ): array
	{
        $tableFields = $this->getTableFields( $tableName );
        if ( $this->getWhereType() ===  "normal" ) {
            foreach ( $fields as $fieldName => $fieldValue ) {
                if ( count( preg_grep( "/int/", $tableFields[ $fieldName ], PREG_GREP_INVERT ) ) === 0 ) {
                    $fieldValue = "'$fieldValue'";
                }
                $quotedValues[ $fieldName ] = $fieldValue;
            }
        } else if ( $this->getWhereType() ===  "like" ) {
            foreach ( $fields as $fieldName => $fieldValue ) {
                $fieldValue = "'%$fieldValue%'";
                $quotedValues[ $fieldName ] = $fieldValue;
            }
        }
        return $quotedValues;
    }
	
    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	private function getTableFields( string $tableName ): array
	{
        $ts = $this->getTableStructure();
        return $ts[ $tableName ][ "fields" ];
    }
	
    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     * @param array $tableStructure tableStructure array
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	private function preppendTablePrefix ( string $tableName, array $fields, bool $assoc = true ): array 
	{
        if ( $assoc ) {
            foreach ( $fields as $fieldName => $fieldValue ) {
                $prefixedFields[ $this->getTablePrefix( $tableName ) . $fieldName ] = $fieldValue;
            }
        } else {
            foreach ( $fields as $fieldName ) {
                $prefixedFields[] = $this->getTablePrefix( $tableName ) . $fieldName;
            }
        }
        return $prefixedFields;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	private function prepareBody ( string $tableName, array $fields, bool $quotes = true ): array
	{
        $fields = $this->preppendTablePrefix( $tableName, $fields );
        if ( $quotes ) {
            $fields = $this->quoteFields( $tableName, $fields );
        }
        return $fields;
    }
	
	private function setWhereComparative ( string $whereComparative ): void
	{
        $this->whereComparative = $whereComparative;
    }






    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function getPrefix (): string
	{
        return $this->prefix;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function getWhereType (): string
	{
        return $this->whereType;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function getWhereComparative (): string
	{
        return $this->whereComparative;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function getName (): string
	{
        return $this->name;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function getHost (): string
	{
        return $this->host;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function getCharset (): string
	{
        return $this->charset;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function isEmpty (): string
	{
        $sql = "SHOW TABLES";
        $preparedSql = parent::prepare( $sql );
        $preparedSql->execute();
        foreach( $preparedSql->fetchAll() as $tableName ) {
            $tables[] = $tableName[ "Tables_in_" . $this->getName() ];
        }
        return count( $tables ) === 0;
    }

    /**
	 * Store table structure into object state.
	 *
	 * @return
     */
	public function createTables (): int
	{
        foreach ( $this->getTableStructure() as $tableName => $tData ) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $this->getPrefix() . "$tableName ( ";
            $prefix = $tData[ "prefix" ];
            foreach ( $tData[ "fields" ] as $fName => $fType ) {
                $pair = "$prefix$fName $fType";
                if ( isset( $tData[ "null" ] ) ) {
                    if ( ! in_array( $fName, $tData[ "null" ] ) ) {
                        $pair .= " NOT NULL";
                    }
                } else {
                    $pair .= " NOT NULL";
                }
                if ( isset( $tData[ "unique" ] ) ) {
                    if ( in_array( $fName, $tData[ "unique" ] ) ) {
                        $pair .= " UNIQUE";
                    }
                }
                if ( isset( $tData[ "default" ] ) ) {
                    if ( in_array( $fName, array_keys( $tData[ "default" ] ) ) ) {
                        $pair .= " DEFAULT " . $tData[ "default" ][ $fName ];
                    }
                }
                if ( $tData[ "primary"] === $fName ) {
                    $pair .= " AUTO_INCREMENT";
                }
                $fieldPairs[] = $pair;
            }
            $fieldPairs[] = "PRIMARY KEY ( $prefix{$tData[ "primary" ]} )";
            $sql .= implode( ", ", $fieldPairs ) . " )";
            $preparedSql = parent::prepare( $sql );
            try {
                $execs[] = $preparedSql->execute();
            } catch ( PDOException $e ) {
                $errors[] = $e->getMessage();
            }
            $fieldPairs = [];
        }
        return count( $execs );
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	public function isTable ( string $tableName ): bool
	{
        return array_key_exists( $tableName, $this->getTableStructure() );
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function setWhereJoinner ( string $joinner ): SQLManager
	{
        $this->whereJoinner = $joinner;
		return $this;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	public function setWhereType ( string $whereType ): SQLManager
	{
        $this->whereType = $whereType;
        if ( $whereType === "like" ) {
            $this->setWhereComparative( "LIKE" );
        }
		return $this;
    }
    
    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	public function setSelectLimit ( int $selectLimit = null, int $selectOffset = null ): SQLManager
	{
        $this->selectLimit = $selectLimit;
        $this->selectOffset = $selectOffset;
		return $this;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
	 *
	 * @return
     */
	public function getTablePrefix( string $tableName ): string
	{
        $ts = $this->getTableStructure();
        return $ts[ $tableName ][ "prefix" ];
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function debug ( bool $debug ): void
	{
        $this->debug = $debug;
    }

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function insert ( string $tableName, array $fields ): bool
	{
        $preparedFields = $this->prepareBody( $tableName, $fields );
        $columns = implode( ",", array_keys( $preparedFields ) );
        $vals = implode( ",", array_values( $preparedFields ) );
        // Joinning pieces
        $sql = "INSERT INTO {$this->getPrefix()}$tableName ( $columns ) VALUES ( $vals )";
        $preparedSql = parent::prepare( $sql );
        $res = $preparedSql->execute();
        // $sql = "SELECT LAST_INSERT_ID()";
        // $preparedSql = parent::prepare( $sql );
        return $res;
    }

    /**
	 *
	 *
     * @param string $tableName DB table name
     * @param array $fields Non-associative array with fields
     * @param array $where Associative array with fields and values
     * @param array $order Associative array with fields and ASC | DESC
	 *
	 * @return
     */
	public function select ( string $tableName, array $fields = [], array $where = [], array $order = [] ): array
	{
        // Handle $fields array
        $fieldsSql = "*";
        if ( count( $fields ) > 0 ) {
            $fieldsSql =  implode( ",", $this->preppendTablePrefix( $tableName, $fields, false ) );
        }
        // Handle $where array
        $whereSql = "";
        if ( count( $where ) > 0 ) {
            $preparedWhere = $this->prepareBody( $tableName, $where );
            foreach ( $preparedWhere as $whereField => $whereValue ) {
                $wherePairs[] = implode( " ", [ $whereField, $this->getWhereComparative(), $whereValue ] );
            }
            $whereSql = implode( " $this->whereJoinner ", $wherePairs );
            $whereSql = "WHERE $whereSql";
        }
        // Handle $order
        $orderSql = "";
        if ( count( $order ) > 0 ) {
            foreach ( $order as $orderField => $orderValue ) {
                $orderPairs[] = "$orderField $orderValue";
            }
            $orderSql = implode( ",", $orderPairs );
            $orderSql = "ORDER BY $orderSql";
        }
        // Handle limit
        $limitSql = "";
        if ( ! empty( $this->selectLimit ) ) {
            $limitSql = "LIMIT $this->selectLimit";
            if ( ! empty( $this->selectOffset ) ) {
                $limitSql = "$limitSql OFFSET $this->selectOffset";
            }
        }
        // Joinning pieces
        $sql = "SELECT $fieldsSql FROM {$this->getPrefix()}$tableName $whereSql $orderSql $limitSql";
        $preparedSql = parent::prepare( $sql );
        $preparedSql->execute();
        $sqlResponse = $preparedSql->fetchAll();
        return array_map( unPrefixAll, $sqlResponse );
    }

    /**
     * @* @param string $tableName DB table name
     * @* @param array $fields Non-associative array with fields
     * @* @param array $where Associative array with fields and values
     * @* @param array $order Associative array with fields and ASC | DESC
     */

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function countTable ( string $tableName, array $where = [] ): int
	{
        // Handle $where array
        $whereSql = "";
        if ( count( $where ) > 0 ) {
            $preparedWhere = $this->prepareBody( $tableName, $where );
            foreach ( $preparedWhere as $whereField => $whereValue ) {
                $wherePairs[] = implode( " ", [ $whereField, $this->getWhereComparative(), $whereValue ] );
            }
            $whereSql = implode( " $this->whereJoinner ", $wherePairs );
            $whereSql = "WHERE $whereSql";
        }
        // Joinning pieces
        $sql = "SELECT COUNT( * ) FROM {$this->getPrefix()}$tableName $whereSql $orderSql $limitSql";
        $preparedSql = parent::prepare( $sql );
        $preparedSql->execute();
        $sqlResponse = $preparedSql->fetch();
        return $sqlResponse[ "COUNT( * )" ];
    }
    
    /**
     * @* @param string $tableName DB table name
     * @* @param array $fields Associative array with fields and values
     * @* @param array $where Associative array with fields and values
     */

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function update ( string $tableName, array $fields, array $where = [] ): bool
	{
        // Handle $fields array
        $preparedFields = $this->prepareBody( $tableName, $fields );
        // $columns = implode( ",", array_keys( $preparedFields ) );
        // $vals = implode( ",", array_values( $preparedFields ) );
        foreach ( $preparedFields as $fieldName => $fieldValue ) {
            $fieldsPairs[] = "$fieldName = $fieldValue";
        }
        $fieldsSql = implode( ",", $fieldsPairs );
        // Handle $where array
        $whereSql = "";
        if ( count( $where ) > 0 ) {
            $preparedWhere = $this->prepareBody( $tableName, $where );
            foreach ( $preparedWhere as $whereField => $whereValue ) {
                $wherePairs[] = "$whereField = $whereValue";
            }
            $whereSql = implode( " $this->whereJoinner ", $wherePairs );
            $whereSql = "WHERE $whereSql";
        }
        // Joinning pieces
        $sql = "UPDATE {$this->getPrefix()}$tableName SET $fieldsSql $whereSql";
        $preparedSql = parent::prepare( $sql );
        return $preparedSql->execute();
    }

    /**
     * @* @param string $tableName DB table name
     * @* @param array $where Associative array with fields and values
     */

    /**
	 * Store table structure into object state.
	 *
     * @param array $tableStructure tableStructure array
     */
	public function delete ( string $tableName, array $where = [] ): bool
	{
        // Handle $where array
        $sql = "TRUNCATE $tableName";
        if ( count( $where ) > 0 ) {
            $preparedWhere = $this->prepareBody( $tableName, $where );
            foreach ( $preparedWhere as $whereField => $whereValue ) {
                $wherePairs[] = "$whereField = $whereValue";
            }
            $whereSql = implode( " $this->whereJoinner ", $wherePairs );
            $whereSql = "WHERE $whereSql";
            $sql = "DELETE FROM {$this->getPrefix()}$tableName $whereSql";
        }
        $preparedSql = parent::prepare( $sql );
        return $preparedSql->execute();
    }
}

