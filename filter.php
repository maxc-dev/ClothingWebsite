<?php
  class Filter {
    public $name;
    public $table;
    public $column;
    public $enum;

    function __construct($name, $table, $column, $enum) {
      $this->name = $name;
      $this->table = $table;
      $this->column = $column;
      $this->enum = $enum;
    }

    function getName() {
      return $this->name;
    }

    function getTable() {
      return $this->table;
    }

    function getColumn() {
      return $this->column;
    }

    function isEnum() {
      return $this->enum;
    }

    function isConditional() {
      return false;
    }

  }

  class ActiveFilter extends Filter {
    public $joinTable;
    public $joinColumn;
    public $comparator;

    function __construct($name, $table, $column, $joinTable, $joinColumn, $comparator) {
      Filter::__construct($name, $table, $column, false, false);
      $this->joinTable = $joinTable;
      $this->joinColumn = $joinColumn;
      $this->comparator = $comparator;
    }

    function getJoinTable() {
      return $this->joinTable;
    }

    function getJoinColumn() {
      return $this->joinColumn;
    }

    function getComparator() {
      return $this->comparator;
    }

  }

  class ConditionalFilter extends Filter {
      public $category;

    function __construct($name, $table, $column, $category) {
      Filter::__construct($name, $table, $column, true);
      $this->category = $category;
    }

    function getCategory() {
      return $this->category;
    }

    function isConditional() {
      return true;
    }

  }

 ?>
