# WPGraphQL for TEC

This is a essentially a fork of the [QL Events](https://github.com/the-events-calendar/ql-events). The primary differences are:
- It treats TEC and WPGraphQL like a first class citizen (using TEC's ORM and functions, implementing WPGraphQL dataloaders, models, and connection resolvers, etc).
- It uses PHP 7.4.
- It follows emerging best practices in the WPGraphQL ecosystem.

For the above reasons, it was easier to start from scratch and borrow old code than to fork the repo.

⚠ **Warning**: This plugin requires the `graphql_connection_type_config` filter (https://github.com/wp-graphql/wp-graphql/pull/2141 ) for connections to work correctly.

Please apply the above PR:

```diff
// wp-graphql/src/Type/WPConnectionType.php:L131
public function __construct( array $config, TypeRegistry $type_registry ) {
+	/**
+	 * Filter the config of WPConnectionType
+	 *
+	 * @param array        $config         Array of configuration options passed to the WPConnectionType when instantiating a new type
+	 * @param WPConnectionType $wp_connection_type The instance of the WPObjectType class
+	 */
+	$config = apply_filters( 'graphql_wp_connection_type_config', $config, $this );

	$this->validate_config( $config );
```

## Progress

### The Events Calendar
- [x] Full query support for Events, Venues, Organizers, and Event Categories, using TEC's ORM. This includes all TEC supported where args on connections.
- [x] All fields for Events, Venues, Organizers, and Event Categories, including JSON-LD data.
- [x] TEC Settings
- [ ] Create, Update, and delete mutations

### Event Tickets
- [x] TEC Settings
- [ ] Attendee type ( fields, model, resolver, connections)
- [ ] Ticket type ( fields, model, resolver, connections)
- [ ] Order type ( fields, model, resolver, connections)
- [ ] Create, Update, Delete, and RSVP/Purchase mutations

### Future
- [ ] Extensions: Events Calendar Pro, Event Tickets Plus, Community Events, Community Tickets, Virtual Events.
