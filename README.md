# Outbiddr Project Roadmap

## Phase 1: Core Models & Database

- Define main models: `User`, `WorkOrder`, `Bid`, `Company`
- Set up migrations with necessary relationships (e.g., `owner_id`, `assigned_user_id`)
- Seed database with sample data for development/testing
- Set up model factories for testing

## Phase 2: Policies & Authorization

- Create `WorkOrderPolicy` and other relevant policies
- Register policies in `AuthServiceProvider`
- Implement route model binding and authorization checks
- Test access control for different user roles (company, contractor, admin)

## Phase 3: Work Orders CRUD

- Build `WorkOrdersController` with index, show, create, update, destroy
- Use Inertia to pass props from Laravel to Vue components
- Render lists of work orders in Vue (with reactive props)
- Add form components for creating/editing work orders

## Phase 4: Bidding System

- Create `Bid` model and controller
- Implement relationships: work order ↔ bids ↔ users
- Build Vue components to submit bids and view bid lists
- Add basic notifications or status updates

## Phase 5: Filtering, Sorting & Notifications

- Implement filters for work orders (e.g., by status, assigned user)
- Allow users to subscribe to filters/tags
- Set up WebSocket or polling notifications for new work orders or bids
- Add sorting options in the UI

## Phase 6: Testing & QA

- Write feature tests for controllers and policies
- Write unit tests for models and utility classes
- Test Inertia page props and Vue components
- Fix bugs, handle edge cases, and improve error handling

## Phase 7: Deployment Prep

- Optimize queries and eager load relationships
- Configure caching where necessary
- Prepare environment for production (queue, storage, mail, etc.)
- Deploy to server and test production environment
