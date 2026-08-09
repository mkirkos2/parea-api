# Parea API Implementation Plan

## Overview
This document outlines the implementation plan for the Parea backend API, which will serve the mobile frontend application.

## Database Schema Updates Needed

### Events Table
The current migration needs to be updated to include all required fields:

`php
Schema::create('events', function (Blueprint ) {
    ->id();
    ->string('title');
    ->text('description');
    ->string('category');
    ->date('date');
    ->time('time');
    ->integer('duration'); // in minutes
    ->string('area');
    ->string('meeting_point');
    ->unsignedBigInteger('host'); // user ID
    ->string('participation_mode'); // 'open' or 'approval'
    ->boolean('first_time_friendly')->default(false);
    ->boolean('mostly_solo')->default(false);
    ->decimal('cost', 8, 2)->default(0); // 0 for free
    ->integer('capacity');
    ->json('requirements')->nullable();
    ->timestamps();
    
    ->foreign('host')->references('id')->on('users')->onDelete('cascade');
});
`

### Additional Migrations to Create

1. **chat_messages table**
2. **reports table**
3. **event_user_participations table** (pivot)
4. **event_user_favorites table** (pivot)

## API Endpoints to Implement

### Authentication
- POST /api/login
- POST /api/register
- POST /api/logout
- GET /api/user

### Events
- GET /api/events
- GET /api/events/{id}
- POST /api/events
- PUT /api/events/{id}
- DELETE /api/events/{id}
- POST /api/events/{id}/join
- POST /api/events/{id}/leave
- POST /api/events/{id}/favorite
- DELETE /api/events/{id}/favorite

### Users
- GET /api/users/{id}
- PUT /api/users/{id}
- GET /api/users/{id}/events
- GET /api/users/{id}/created-events
- GET /api/users/{id}/favorites

### Chat
- GET /api/events/{id}/messages
- POST /api/events/{id}/messages

### Reports
- POST /api/reports

## Controllers to Implement

1. AuthController
2. EventController
3. UserController
4. ChatController
5. ReportController

## Form Requests for Validation

1. StoreEventRequest
2. UpdateEventRequest
3. StoreReportRequest
4. JoinEventRequest

## API Resources for JSON Responses

1. EventResource
2. EventCollection
3. UserResource
4. ChatMessageResource
5. ReportResource

## Implementation Phases

### Phase 1: Foundation
- Update database migrations
- Implement authentication endpoints
- Create basic user functionality

### Phase 2: Events Core
- Implement events CRUD operations
- Add event participation functionality
- Add favorites functionality

### Phase 3: Social Features
- Implement chat functionality
- Add reporting system

### Phase 4: Advanced Features
- Add search and filtering
- Implement notifications
- Add admin functionality

## Testing Strategy
- Unit tests for all models
- Feature tests for all API endpoints
- Database factory for test data
