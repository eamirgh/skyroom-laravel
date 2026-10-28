# API Reference Overview

The `Skyroom` wrapper provides two ways to interact with the API:
1. **Fluent Resource Managers**: `Skyroom::rooms()`, `Skyroom::users()`, `Skyroom::services()`
2. **Direct Shortcut Methods**: `Skyroom::getRooms()`, `Skyroom::createRoom(...)`, etc.

Both interfaces invoke the exact same underlying logic.

---

## Method Matrix

| Skyroom Action | Resource Method | Shortcut Method | Description |
| :--- | :--- | :--- | :--- |
| `getServices` | `Skyroom::services()->all()` | `Skyroom::getServices()` | List all active/purchased services |
| `getRooms` | `Skyroom::rooms()->all()` | `Skyroom::getRooms()` | List all created rooms |
| `countRooms` | `Skyroom::rooms()->count()` | `Skyroom::countRooms()` | Get total number of rooms |
| `getRoom` | `Skyroom::rooms()->get(...)` | `Skyroom::getRoom(...)` | Get room by ID or name |
| `getRoomUrl` | `Skyroom::rooms()->url(...)` | `Skyroom::getRoomUrl(...)` | Get permanent room link |
| `createRoom` | `Skyroom::rooms()->create(...)` | `Skyroom::createRoom(...)` | Create a new room |
| `updateRoom` | `Skyroom::rooms()->update(...)` | `Skyroom::updateRoom(...)` | Update room attributes |
| `deleteRoom` | `Skyroom::rooms()->delete(...)` | `Skyroom::deleteRoom(...)` | Delete a room |
| `getRoomUsers` | `Skyroom::rooms()->users(...)` | `Skyroom::getRoomUsers(...)` | List users with room access |
| `addRoomUsers` | `Skyroom::rooms()->addUsers(...)` | `Skyroom::addRoomUsers(...)` | Grant users room access |
| `removeRoomUsers` | `Skyroom::rooms()->removeUsers(...)` | `Skyroom::removeRoomUsers(...)` | Revoke users room access |
| `updateRoomUser` | `Skyroom::rooms()->updateUser(...)` | `Skyroom::updateRoomUser(...)` | Update user access level in room |
| `getUsers` | `Skyroom::users()->all()` | `Skyroom::getUsers()` | List all users |
| `countUsers` | `Skyroom::users()->count()` | `Skyroom::countUsers()` | Get total user count |
| `getUser` | `Skyroom::users()->get(...)` | `Skyroom::getUser(...)` | Get user by ID or username |
| `createUser` | `Skyroom::users()->create(...)` | `Skyroom::createUser(...)` | Create new user account |
| `updateUser` | `Skyroom::users()->update(...)` | `Skyroom::updateUser(...)` | Update user details |
| `deleteUser` | `Skyroom::users()->delete(...)` | `Skyroom::deleteUser(...)` | Delete a single user |
| `deleteUsers` | `Skyroom::users()->deleteMany(...)` | `Skyroom::deleteUsers(...)` | Batch delete users |
| `getUserRooms` | `Skyroom::users()->rooms(...)` | `Skyroom::getUserRooms(...)` | List rooms assigned to a user |
| `addUserRooms` | `Skyroom::users()->addRooms(...)` | `Skyroom::addUserRooms(...)` | Assign rooms to user |
| `removeUserRooms` | `Skyroom::users()->removeRooms(...)` | `Skyroom::removeUserRooms(...)` | Unassign rooms from user |
| `createLoginUrl` | *Direct method* | `Skyroom::createLoginUrl(...)` | Generate instant SSO login URL |
| *(Raw action)* | `Skyroom::call($action, $params)` | `Skyroom::call(...)` | Call any raw/unwrapped action |
