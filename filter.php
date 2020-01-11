<?php

  /*
    this class contains all the code for the Filter class and the subclasses
    that inherit it. the Filter class is the default filter for filtering
    product results. Fitler is used for static filters such as gender when the
    only values are Male or Female.

    ActiveFilter is a subclass of Filter which only shows filters if they
    are in use. For example if there are no Hoodies in stock, it won't list
    Hoodies as an option.

    ConditionalFilter is also a subclsas of Filter which will only display the
    filter if it's parent category is available. For example if you had the
    parent category be "Shoes", it won't display filters for Sleeve Length. But
    if the Hoodies category is selected, Sleeve length will show.
  */

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
