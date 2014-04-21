<?php namespace Laracasts\TestDummy;

use TestCase, Artisan, DB;

class DbTestCase extends TestCase {

	/**
	 * Setup the DB before each test
	 */
	public function setUp()
	{
		parent::setUp();

		// This should only apply for Sqlite DBs
		// in memory.
		Artisan::call('migrate');

		// For anything else, we'll run all tests
		// through a transaction, and then rollback
		// afterward.
		DB::beginTransaction();
	}

	/**
	 * Rollback transactions after each test
	 */
	public function tearDown()
	{
		DB::rollback();
	}

}