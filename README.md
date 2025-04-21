# Laravel Movie DB

A movie database system built with Laravel for the Software Construction and Evolution course at the Software Engineering Technology Program, Department of Information Technology, Politeknik Negeri Padang.

## Setup Instructions

To set up this project after cloning the repository, follow these steps:

1. **Install Dependencies**:

    ```bash
    composer install
    ```

2. **Environment Setup**:

    ```bash
    cp .env.example .env
    ```

    Configure your database settings in the `.env` file.

3. **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

4. **Run Migrations**:

    ```bash
    php artisan migrate
    ```

5. **Run Seeds** (Optional):

    ```bash
    php artisan db:seed
    ```

6. **Start Development Server**:
    ```bash
    php artisan serve
    ```
    The application will be available at http://localhost:8000.

## Refactoring Documentation

The MovieController has been refactored to improve code quality and maintainability. Here's a detailed breakdown of the refactorings:

### 1. Validation Logic Refactoring

-   **What**: Extracted duplicate validation rules from `store` and `update` methods
-   **How**: Created a private `validateMovie()` method that handles both creation and update scenarios
-   **Benefits**:
    -   Reduced code duplication
    -   Centralized validation rules
    -   Made validation logic reusable
    -   Easier to maintain and update validation rules

### 2. Search Functionality Refactoring

-   **What**: Extracted search logic from the `index` method
-   **How**:
    -   Created a private `searchMovies()` method in the controller
    -   Added a `scopeSearch()` method to the Movie model as an alternative approach
-   **Benefits**:
    -   Simplified the `index` method
    -   Made search functionality reusable
    -   Improved readability
    -   Better separation of concerns

### 3. File Handling Refactoring

-   **What**: Extracted image upload, storage, and deletion logic
-   **How**: Created a dedicated `FileService` class with methods:
    -   `saveImage()`: Handles image uploads and storage
    -   `deleteImage()`: Handles image deletion
-   **Benefits**:
    -   Centralized file operations
    -   Removed duplicate code across methods
    -   Made file handling logic reusable across the application
    -   Better testability of file operations

### 4. Form Request Validation

-   **What**: Moved validation rules to dedicated Form Request classes
-   **How**: Created:
    -   `MovieStoreRequest`: For movie creation validation
    -   `MovieUpdateRequest`: For movie update validation
-   **Benefits**:
    -   Cleaner controller methods
    -   Validation logic is isolated and reusable
    -   Better organization of code
    -   Follows Laravel best practices

### 5. Delete Method Refactoring

-   **What**: Improved the `delete` method by extracting file deletion logic
-   **How**: Leveraged the `FileService` to handle image deletion
-   **Benefits**:
    -   Consistent approach to file deletion
    -   Removed duplicate code
    -   Improved maintainability

### 6. Dependency Injection

-   **What**: Added proper dependency injection for the FileService
-   **How**: Added a constructor to inject the FileService
-   **Benefits**:
    -   Better testability
    -   Follows SOLID principles
    -   More flexible architecture

### 7. Model Enhancement

-   **What**: Added query scope to the Movie model
-   **How**: Implemented `scopeSearch()` method in the Movie model
-   **Benefits**:
    -   More reusable search functionality
    -   Better adherence to Laravel conventions
    -   Cleaner controller code

## Overall Benefits of the Refactoring

1. **Reduced Code Duplication**: Eliminated repeated code patterns across methods
2. **Improved Maintainability**: Easier to update and maintain with centralized logic
3. **Better Separation of Concerns**: Each class and method has a clear, single responsibility
4. **Enhanced Testability**: Isolated components are easier to test
5. **Follows SOLID Principles**: Better adherence to software design principles
6. **Follows Laravel Best Practices**: Aligns with Laravel's recommended patterns and practices
7. **Improved Code Organization**: Clearer structure makes the code easier to understand

_Original credit: Yori Adi Atma_
