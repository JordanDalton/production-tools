Version 1.6
==========================================================================

This version includes several enhancements to user objects and repositories, as well as a couple of fixes to work better with version 8 of A+.

* Bug #470: Version 8 of A+ breaks BOM queries (company number should be zero)
* Bug #509: Forecast data BOM queries don't account for multiple effective dates on a BOM
* Feature #501: Create new basic object for Let's Gel users
* Feature #502: Create interface for user objects to implement
* Feature #503: Add check to user object for whether Active Directory user is disabled
* Feature #504: Show username as display name for WRS user when no LDAP data
* Feature #505: Add repository for retrieving Let's Gel users
* Feature #515: Create new salt interface and generator for 64-bit integers
* Feature #516: Create new hash interface and generator for SHA-1 hashes
* Feature #517: Create auth adapter for Let's Gel users
* Feature #533: Add `isMemberOf()` method to `WrsUser` class


Version 1.5
==========================================================================

This version includes domain objects, interfaces, and decorators related to items and BOMs, miscellaneous view helpers, and a class that helps with e-mailing application errors in a production environment.

* Feature #437: Add domain object to represent item in A+/iSeries, along with decorator for description
* Feature #438: New view helper for outputting clean item descriptions
* Feature #439: Create domain objects for BOMs
* Feature #440: Add view helper for incrementing and outputting a variable
* Feature #442: Interfaces for BOM-related objects
* Feature #449: Add decorator for outputting useful information about an item number suffix for Let's Gel mats
* Feature #450: Add view helper for decorator for mat suffixes
* Feature #457: New view helper for easily formatting ISO/SQL dates as U.S. dates
* Feature #463: New class to be used for e-mailing errors

Version 1.4
==========================================================================

* Bug #208: Output of `toArray()` doesn't always match what you would expect
* Bug #219: Current implementation of `lastInsertId()` doesn't work for DB2 on AS400
* Bug #360: New `findAllBy()` method only returns one record, not all
* Bug #377: `getValues()` doesn't work when the record set is instantiated with PHP standard objects
* Bug #378: `getUserGroups()` triggers error when no 'memberof' attribute in LDAP data
* Feature #172: Add abstract user classes / methods, WrsUser classes
* Feature #187: Add new Excel action helper class
* Feature #191: `toArray()` in domain objects should include protected properties not in `$_data`
* Feature #207: Create password generator class
* Feature #215: Add built-in indexing functionality to RecordSet class
* Feature #217: Add forecastColumn to the list of columns retrieved in `getMats()` method in mats table
* Feature #221: Add new validator class for checking that two values match (i.e., password confirmation)
* Feature #227: Add `getOrganization()` as a method all classes must implement that extend the UserAbstract class
* Feature #228: Add verify password method to Let's Gel users table class
* Feature #229: Adjust the way user class constants and `getOrganization()` works
* Feature #232: Add `getUserType()` method to WRS user object and abstract object
* Feature #233: Add interface for user repositories
* Feature #234: Update WRS user repository to be able to use cache when retrieving user
* Feature #264: Add "reply-to" address to mail object and mailer class
* Feature #346: LDAP: Have `getUserInfo()` return null if user not found, instead of throwing an exception
* Feature #361: Add ability for `getPopulated()` to return array with different field names
* Feature #362: Add `unpopulate()` method to domain object
* Feature #420: Add new filter for appending an order generation number of zero if only order is given
* Feature #422: Change behavior of domain object to ignore extra properties that aren't specified in the object
* Feature #423: Action helper for generating Word document from a controller/view

---------------------------

Changes for 1.3.2
--

A couple of features for working with tables from AS/400.

* Feature #175: Add support for getting LG shipment information about only one fabric
* Feature #178: New query for open orders for fabrics, for certain customers

---------------------------

Changes for 1.3.1
--

Mostly new database features; very basic implementation of users.

* Bug #135: Delete unused classes/methods
* Bug #147: Forecast sorts by month in weird order
* Feature #134: Query for fabrics/mats in terms of yards rather than mat units
* Feature #143: Add query to group and total LG fabric shipments by item number
* Feature #151: Add ability to unlock items in ITBAL
* Feature #156: Add ability to get data for one specific fabric and its children
* Feature #158: Add new table for Let's Gel users
* Feature #159: Add basic user/group objects and user repository
* Feature #164: Add class for item master table and ability to unlock items in that table
* Feature #165: Add ability to check for locked items to itbal table class
* Feature #167: Create new POHED table class for unlocking POs

---------------------------

Changes for 1.3
--

New database features. LgItemManager and LgBuildPlan applications are using the shared database table classes.

* Feature #85: Create separate folders for library and tests
* Feature #99: Create new action controller helper for CSV file context
* Feature #103: Create table classes for tables on IBM i
* Feature #107: Improve handling of schemas in ODBC/DB2 adapter
* Feature #108: Support ability to use a select object as a table

---------------------------

Changes for 1.2.2
--

* Feature #84: New database adapter for Lotus Notes via ODBC

---------------------------

Changes for 1.2.1
--

* `WrsGroup_Date` now has fluent interface for relevant methods, so you can do this, for example:

    $date->addBusinessDays(2)->toString('yyyyMMdd')

* `WrsGroup_Model_RecordSet` now has an `init()` method for extending classes

---------------------------

Changes for 1.2.0
--

BUG FIXES

* In Db package, fixed a bug in method for joining one table to another; was referencing wrong property

FEATURES

* In Model package, added new options array for creating a new domain object; the only option currently supported is to ignore nulls, which is set to true by default. This means that when you instantiate a domain object, if there are any propertites in the data array set to null, those properties will be ignored and not checked to see if they are a valid property of that object. This will retain better backwards compatibility.

----------------------------

Changes for 1.2pre2
--

Merged changes from 1.1.1

----------------------------

Changes for 1.1.1
--

BUG FIXES

* Fixed a bug in abstract domain model class; missing $ that didn't allow properties defined separately as objects to be retrieved by referring to a public property

FEATURES

[Abstract domain objects]

* Domain objects now throw an exception if you are trying to access a property that has not been defined in the object
* Calling setPropertyName() on a domain object returns the domain object for fluent interface
* Refactored domain object class for more consistency in getting/setting data and telling which items have been populated

[Other]

* saveToTable() method of abstract repository class now filters out objects and arrays
* New generic models for e-mail address and mail domain objects
* New wrapper for Zend_Mail service
* Abstract factory class marked as deprecated
* Record set class refactored so as not to make use of abstract factory class
