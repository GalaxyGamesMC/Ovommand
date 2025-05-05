CommandParameterData *__fastcall CommandRegistry::buildOverload<CommandParameterData>(
    __int64 a1,                                       // Not used directly in this function but likely represents some context (e.g., registry data).
    __int64 a2,                                       // Pointer to the overload being constructed (manages a vector of CommandParameterData).
    const struct CommandParameterData *a3,           // Pointer to the primary command parameter data to add.
    const struct CommandParameterData *a4            // Pointer to additional command parameter data to add.
)
{
    CommandParameterData *v4; // Pointer to the current end of the `CommandParameterData` vector.
    __int64 v5;              // Pointer to the metadata for managing the vector (start, end, capacity).
    CommandParameterData *result; // Return value.

    // Get a pointer to the current position of the first free slot in the parameter data vector.
    v4 = *(CommandParameterData **)(a2 + 24);

    // `v5` points to the vector's management metadata (start, current end, and capacity pointers).
    v5 = a2 + 16;

    // Check if the vector is full (i.e., end pointer == capacity pointer).
    if ( *(CommandParameterData **)(a2 + 32) == v4 )
    {
        // Reallocate the vector and add a new element.
        std::vector<CommandParameterData>::_Emplace_reallocate<CommandParameterData const &>(
            v5,                // Address of the managing metadata for the vector.
            *(_QWORD *)(a2 + 24), // Pointer to the current end of the vector.
            a3                 // Add `a3` to the newly reallocated space.
        );
    }
    else // If the vector is not full...
    {
        // Construct a new CommandParameterData instance at the current end of the vector using `a3`.
        CommandParameterData::CommandParameterData(v4, a3);

        // Increment the vector's current end pointer by the size of a CommandParameterData object (80 bytes).
        *(_QWORD *)(v5 + 8) += 80LL;
    }

    // Check if the vector now needs reallocation (i.e., the current end = capacity).
    if (*(_QWORD *)(v5 + 16) == *(_QWORD *)(v5 + 8))
    {
        // Reallocate and add `a4` to the newly allocated space.
        return (CommandParameterData *)std::vector<CommandParameterData>::_Emplace_reallocate<CommandParameterData const &>(
            v5,               // Address of the managing metadata for the vector.
            *(_QWORD *)(v5 + 8), // Current end of the vector.
            a4                // Add `a4` to the newly reallocated space.
        );
    }

    // If no reallocation was needed, construct a new CommandParameterData instance for `a4` at the current end of the vector.
    result = CommandParameterData::CommandParameterData(*(CommandParameterData **)(v5 + 8), a4);

    // Update the current end pointer of the vector.
    *(_QWORD *)(v5 + 8) += 80LL;

    // Return the most recently added CommandParameterData instance.
    return result;
}