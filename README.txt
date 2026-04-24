=== QL Events ===
Contributors: theeventscalendar, stellarwp, kidunot89
Tags: GraphQL, TEC, WPGraphQL, Events, Tickets
Requires at least: 6.7
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Maintained at: https://github.com/the-events-calendar/ql-events

== Description ==
Adds The Events Calendar and Event Tickets functionality to the WPGraphQL schema.

== Changelog ==

= [0.3.2] 2026-04-23 =

**Security:**

* Require authentication and `edit_post` capability on the parent event to register or update attendees via the GraphQL `registerAttendee` and `updateAttendee` mutations. Both checks are filterable via `ql_events_user_can_register_attendee` and `ql_events_user_can_update_attendee` for sites that need to extend access (e.g. frontend self-registration).

= [0.3.0] 2023-07-26 =

**New Features:**

* QL Events settings tab added and schema organized.
* Events Virtual support added.
* Attendee interface, connections, and mutations added. Order interface added.
* Custom ticket field types and queries implemented.

= [0.1.0] 2022-08-12 =

**Breaking changes:**

* Updated testing and CI configuration.

**Fixed:**

* Support for WPGraphQL v1.8+ has been added.
* Removed extraneous comma to prevent a silent PHP error.
