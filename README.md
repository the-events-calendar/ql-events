# WPGraphQL for TEC

This is a essentially a fork of the [QL Events](https://github.com/the-events-calendar/ql-events). The primary differences are:
- It treats TEC and WPGraphQL like a first class citizen (using TEC's ORM and functions, implementing WPGraphQL dataloaders, models, and connection resolvers, etc).
- It uses PHP 7.4.
- It follows emerging best practices in the WPGraphQL ecosystem.

For the above reasons, it was easier to start from scratch and borrow old code than to fork the repo.

## Features

- [x] Full query support for Events, Venues, Organizers, and Event Categories, using TEC's ORM. This includes all TEC supported where args on connections.
- [x] All fields for Events, Venues, Organizers, and Event Categories.
- [ ] JsonLD data for objects.
- [ ] TEC Settings
- [ ] Create, Update, and delete mutations
- [ ] Extensions: Events Calendar Pro, Event Tickets, Event Tickets Plus, Events, Community Events, Community Tickets, Virtual Events.
- [ ] Unit tests
