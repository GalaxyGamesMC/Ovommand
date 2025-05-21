/**
 * Validates if a value is within a specified range and outputs an error message if not.
 * 
 * @param value The value to check
 * @param minValue The minimum allowed value (inclusive)
 * @param maxValue The maximum allowed value (inclusive)
 * @param cmdOutput Pointer to CommandOutput for error message handling
 * @return bool Returns true if value is within range, false otherwise
 */
char __fastcall Command::validRange(
    unsigned int value,
    unsigned int minValue, 
    unsigned int maxValue,
    struct CommandOutput* cmdOutput
) {
    // Check if value is below minimum
    if (static_cast<int>(value) < static_cast<int>(minValue)) {
        // Convert values to strings for error message
        _BYTE valueStr[32];
        _BYTE minValueStr[32];
        _BYTE tempBuffer[32];
        int padding1 = 0;
        int padding2 = 0;

        // Convert numbers to strings
        Util::toString<int,0,0>(valueStr, value);
        Util::toString<int,0,0>(minValueStr, minValue);
        
        // Prepare parameters for error message
        _BYTE* msgParams[2] = { valueStr, tempBuffer };
        __int64 vectorStorage;
        __int128 vectorExtra;
        std::vector<CommandOutputParameter>::vector<CommandOutputParameter>(&vectorStorage, &msgParams, nullptr);

        // Prepare error message
        __int128 messageStorage = 0LL;
        char* errorMsg = static_cast<char*>(std::allocator<char>::allocate(&messageStorage, 32LL));
        *reinterpret_cast<__int64*>(&messageStorage) = reinterpret_cast<__int64>(errorMsg);
        __int64 msgLength = 29LL;
        unsigned __int64 bufferSize = 31LL;
        __int64 totalSize = 31LL;
        
        // Set error message for too small value
        strcpy(errorMsg, "commands.generic.num.tooSmall");

        // Output error message if command output is available
        if (*reinterpret_cast<_DWORD*>(cmdOutput)) {
            CommandOutput::addMessage(cmdOutput, &messageStorage, &vectorStorage, 1LL);
            bufferSize = totalSize;
            errorMsg = reinterpret_cast<char*>(messageStorage);
        }

        // Cleanup allocated memory
        cleanupMessageBuffer(errorMsg, bufferSize);
        cleanupVectorStorage(&vectorStorage, vectorExtra);
        
        return 0;
    }

    // Check if value is above maximum
    if (static_cast<int>(value) > static_cast<int>(maxValue)) {
        // Similar structure as above, but for too big value
        _BYTE valueStr[32];
        _BYTE maxValueStr[32];
        _BYTE tempBuffer[32];
        int padding1 = 0;
        int padding2 = 0;

        Util::toString<int,0,0>(valueStr, value);
        Util::toString<int,0,0>(maxValueStr, maxValue);
        
        _BYTE* msgParams[2] = { valueStr, tempBuffer };
        __int64 vectorStorage;
        __int128 vectorExtra;
        std::vector<CommandOutputParameter>::vector<CommandOutputParameter>(&vectorStorage, &msgParams, nullptr);

        __int128 messageStorage = 0LL;
        char* errorMsg = static_cast<char*>(std::allocator<char>::allocate(&messageStorage, 32LL));
        *reinterpret_cast<__int64*>(&messageStorage) = reinterpret_cast<__int64>(errorMsg);
        __int64 msgLength = 27LL;
        unsigned __int64 bufferSize = 31LL;
        __int64 totalSize = 31LL;
        
        // Set error message for too big value
        strcpy(errorMsg, "commands.generic.num.tooBig");

        if (*reinterpret_cast<_DWORD*>(cmdOutput)) {
            CommandOutput::addMessage(cmdOutput, &messageStorage, &vectorStorage, 1LL);
            bufferSize = totalSize;
            errorMsg = reinterpret_cast<char*>(messageStorage);
        }

        cleanupMessageBuffer(errorMsg, bufferSize);
        cleanupVectorStorage(&vectorStorage, vectorExtra);
        
        return 0;
    }

    // Value is within range
    return 1;
}

// Helper function to cleanup message buffer
private void cleanupMessageBuffer(char* buffer, unsigned __int64 size) {
    if (size >= 0x10) {
        char* originalBuffer = buffer;
        if (size + 1 >= 0x1000) {
            buffer = reinterpret_cast<char*>(*(reinterpret_cast<__int64*>(buffer) - 1));
            if (static_cast<unsigned __int64>(originalBuffer - buffer - 8) > 0x1F) {
                _invalid_parameter_noinfo_noreturn();
            }
        }
        std::_Return_temporary_buffer<unsigned int>(buffer);
    }
}

// Helper function to cleanup vector storage
private void cleanupVectorStorage(__int64* storage, __int128& extra) {
    if (*storage) {
        std::_Destroy_range<std::allocator<std::pair<std::string,enum VolumeAreaCommand::Mode>>>(*storage, extra, storage);
        __int64 ptr = *storage;
        if (static_cast<unsigned __int64>(40 * ((*(reinterpret_cast<__int64*>(&extra) + 1) - *storage) / 40)) >= 0x1000) {
            ptr = *reinterpret_cast<__int64*>(*storage - 8);
            if (static_cast<unsigned __int64>(*storage - ptr - 8) > 0x1F) {
                _invalid_parameter_noinfo_noreturn();
            }
        }
        std::_Return_temporary_buffer<unsigned int>(ptr);
        *storage = 0LL;
        extra = 0LL;
    }
}