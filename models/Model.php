<?php
	//error_reporting(0);
	require_once(__DIR__.'/../config/AppConfig.php');

	/**
	* 
	*/
	class Model
	{
		public $query;
		public $values = array();
		public $table;

		function __construct()
		{}

		/**
		 * @param (array) columns
		 */
		public function select($columns, $field = '') {
			$f = '';
			$this->query = '';
			if($columns[0] == '*') {
				$f = ' '.$columns[0];
			}
			else {
				for($i = 0; $i < count($columns); $i++) {
					if($i == count($columns)-1) {
						$f .= ' '.$columns[$i];
					}
					else {
						$f .= ' '.$columns[$i].',';	
					}
				}
			}
			$this->query .= 'SELECT '.$field.''.$f.' FROM vicinvent.'.$this->table;

			return $this;
		}

		/**
		 * @param (string) field, (string) condition, (any type) newVal
		 */
		public function where($field, $newVal, $condition = '', $tab = '') {
			if($tab != '') {
				$this->query .= ' WHERE '.$tab.'.';
			}
			else {
				$this->query .= ' WHERE ';
			}

			if($condition != ''){
				$q = $field.' '.$condition.' ?';
			}
			else{
				$q = $field.' = ?';	
			}
			$this->query .= $q;
			$newVal = explode(' ', $newVal);
			$this->values = array_merge($this->values, $newVal);
			return $this;
		}

		/**
		 * @param (string) field, (string) condition, (any type) newVal
		 */
		public function and($field, $newVal, $condition = '') {
			$this->query .= ' AND ';

			if($condition != ''){
				$q = $field.' '.$condition.' ?';
			}
			else{
				$q = $field.' = ?';	
			}
			$this->query .= $q;
			$newVal = explode(' ', $newVal);
			$this->values = array_merge($this->values, $newVal);

			return $this;
		}

		/**
		 * @param (string) field, (string) condition, (any type) newVal
		 */
		public function or($field, $newVal, $condition = '') {
			$this->query .= ' OR ';

			if($condition != ''){
				$q = $field.' '.$condition.' ?';
			}
			else{
				$q = $field.' = ?';	
			}
			$this->query .= $q;
			$newVal = explode(' ', $newVal);
			$this->values = array_merge($this->values, $newVal);

			return $this;
		}

		/**
		 * @param (string) field, (string) condition, (any type) newVal
		 */
		public function all() {
			$this->query = '';
			$this->query = 'SELECT * FROM vicinvent.'.$this->table;
			$db = $GLOBALS['db'];

			try {
				$stmt = $db->prepare($this->query);
				$stmt->execute();

				return $stmt->fetchAll();
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

		public function get($number = 0) {
			$db = $GLOBALS['db'];
			
			try {
				if($number == 0) {
					$stmt = $db->prepare($this->query);
					if($stmt->execute($this->values)) {
						$this->query = '';
						$this->values = array();
						return $stmt->fetchAll();
					}
				}
				else if($number == 1) {
					$stmt = $db->prepare($this->query);
					if($stmt->execute($this->values)) {
						$this->query = '';
						$this->values = array();
						return $stmt->fetch();
					}
				}
				else {
					$this->query .= ' LIMIT '.$number;

					$stmt = $db->prepare($this->query);
					if($stmt->execute($this->values)) {
						$this->query = '';
						$this->values = array();
						return $stmt->fetchAll();
					}
				}
				
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

		/**
		 * @param (array) fields, (array) values
		 */
		public function insert($columns, $values) {
			$f = $this->table.'(';
			$v = 'VALUES(';
			for($i = 0; $i < count($columns); $i++) {
				if($i == count($columns)-1) {
					$f .= $columns[$i].')';
				}
				else {
					$f .= $columns[$i].', ';
				}
			}

			for($i = 0; $i < count($values); $i++) {
				if($i == count($values)-1) {
					$v .= '?)';
				}
				else {
					$v .= '?, ';	
				} 
			}
			$this->query = 'INSERT INTO vicinvent.'.$f.' '.$v;
			$db = $GLOBALS['db'];

			try {
				$stmt = $db->prepare($this->query);
				if($stmt->execute($values)) {
					return $db->lastInsertId();
				}
				else {
					return false;
				}
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

		/**
		 * @param (array) columns, (array) values
		 */
		public function update($columns, $values) {
			$f = $this->table.' SET ';
			for($i = 0; $i < count($columns); $i++) {
				if($i == count($columns)-1) {
					$f .= $columns[$i].' = \''.$values[$i].'\'';
				}
				else {
					$f .= $columns[$i].' = \''.$values[$i].'\', ';
				}
			}
			$this->query = 'UPDATE vicinvent.'.$f;

			return $this;
		}

		public function orderBy($column, $cond = 'ASC') {
			$this->query .= ' ORDER BY '.$column.' '.$cond;
			return $this;
		}

		/**
		 * @param (string) val
		 */

		public function hash($val) {
			return hash('md5', $val);
		}

		public function checkEmail($email) {
			$this->query = '';
			$this->query = 'SELECT * FROM vicinvent.'.$this->table.' WHERE email = ?';

			$db = $GLOBALS['db'];
			
			try {
				$stmt = $db->prepare($this->query);
				$stmt->execute([$email]);
				if(gettype($stmt->fetch()) == 'boolean') {
					return true;
				}
				else {
					return false;
				}
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

		public function search($column, $keyword) {
			$this->query = 'SELECT DISTINCT * FROM vicinvent.'.$this->table.' WHERE '.$column.' ';
			$keyArr = explode(' ', $keyword);
			//var_dump($keyArr); die();
			for($i=0; $i<count($keyArr); $i++) {
				if($i == 0) {
					$this->query .= 'LIKE \'%'.$keyArr[$i].'%\'';
				}
				else {
					$this->query .= ' OR '.$column.' LIKE \'%'.$keyArr[$i].'%\'';
				}
				
			}

		    $db = $GLOBALS['db'];
			
			try {
				$stmt = $db->prepare($this->query);
				$stmt->execute();

				$files = $stmt->fetchAll();

				return $files;
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

		/*public function ijoin($table, $column, $j_table, $j_column) {
			$this->query .= ' INNER JOIN vicinvent.'.$table.' ON vicinvent.'.$j_table.'.'.$j_column.'='.$table.'.'.$column;

			return $this;
		}

		public function rjoin($table, $column, $j_table, $j_column) {
			$this->query .= ' RIGHT JOIN vicinvent.'.$table.' ON vicinvent.'.$j_table.'.'.$j_column.'='.$table.'.'.$column;

			return $this;
		}

		public function checkVolume($volume, $year, $sub_department) {
			$this->query = '';
			$this->query = 'SELECT * FROM vicinvent.volumes WHERE name = ? AND year = ? AND sub_department_id = ?';

			$db = $GLOBALS['db'];
			
			try {
				$stmt = $db->prepare($this->query);
				$stmt->execute([$volume, $year, $sub_department]);
				if(gettype($stmt->fetch()) == 'boolean') {
					return true;
				}
				else {
					return false;
				}
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}*/

		public function delete() {
			$this->query = 'DELETE FROM '.$this->table;

			return $this;	
		}

		public function exec() {
			$db = $GLOBALS['db'];
			
			try {
				//var_dump($this->query); die();
				$stmt = $db->prepare($this->query);
				if($stmt->execute($this->values)) {
					$this->query = '';
					$this->values = [];
					return true;
				}
				else {
					return false;
				}
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

	}