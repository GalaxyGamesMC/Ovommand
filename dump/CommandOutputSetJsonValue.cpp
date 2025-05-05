/**
 * This function sets a JSON-like value in the command output.
 * It dynamically allocates memory, copies string data if provided, and updates
 * the target data structure (using `Json::Value`).
 * 
 * @param commandOutput The output object containing JSON or string-based data structures.
 * @param key           Pointer to a constant character representing the key to update.
 * @param value         Pointer to a constant character representing the value to set, or `nullptr`.
 */
void __fastcall CommandOutput::set<char const *>(__int64 commandOutput, _BYTE *key, _BYTE *value)
{
    __int64 jsonReferenceOffset;   // Offset for the JSON value reference
    size_t keyLength;             // Length of the key (calculated)
    bool isKeyResolved;           // Helper flag to determine if the key is resolved
    const char *assignedKey;      // Pointer to the final resolved key
    Json::Value *jsonTarget;      // Pointer to the resolved JSON reference
    __int64 valueLength;          // Length of the value string, if provided
    size_t allocatedLength;       // Length of memory to allocate for the value, if needed
    _BYTE *allocatedValue;        // Pointer to the newly allocated value
    _BYTE *finalBlockToSet;       // Final block to assign into the output
    void **destructionTarget;     // Target for deallocating memory
    unsigned __int64 memorySize;  // Memory size to deallocate
    char *deallocateBlock;        // Pointer for deallocation helper
    void *temporaryBlock;         // Temporarily allocated memory
    unsigned int allocationFlags; // Flags used for allocation and cleanup
    char *keyBuffer[2];           // Buffer for processing the key
    __m128i simKeyStorage;        // Simulated key storage for processing
    __int64 paddingValue = -2LL;  // Helper value for alignment or safety checks

    // Ensure the command output's validity before proceeding
    if (*(_DWORD *)commandOutput == 4)
    {
        jsonReferenceOffset = *(_QWORD *)(commandOutput + 8);  // Get JSON offset reference
        simKeyStorage = _mm_load_si128((const __m128i *)&_xmm); // Load helper value
        LOBYTE(keyBuffer[0]) = 0;            // Initialize key buffer
        keyLength = -1LL;

        // Calculate null-terminated string length for the key
        do {
            ++keyLength;
        } while (key[keyLength]);

        // Assign the resolved key
        std::string::assign(keyBuffer, key, keyLength);
        assignedKey = (const char *)keyBuffer;

        // If the buffer length exceeds 16 characters, use `keyBuffer[0]` instead
        if (simKeyStorage.m128i_i64[1] >= 0x10uLL)
            assignedKey = keyBuffer[0];

        // Resolve the JSON reference for the key
        jsonTarget = Json::Value::resolveReference(
            (Json::Value *)(jsonReferenceOffset + 8),
            assignedKey,
            isKeyResolved
        );

        allocationFlags = allocationFlags & 0xFFFFFE00 | 0x104;

        // If the value is provided, calculate the length and allocate memory for it
        if (value)
        {
            valueLength = -1LL;

            // Calculate null-terminated string length for the value
            do {
                ++valueLength;
            } while (value[valueLength]);

            // Determine allocation length
            allocatedLength = (unsigned int)(valueLength + 1);
            if ((_DWORD)valueLength == -1)
                allocatedLength = -1LL;

            // Allocate memory for the value
            allocatedValue = (_BYTE *)malloc(allocatedLength);
            
            // If allocation succeeded, copy the value into memory
            if (allocatedValue)
            {
                memcpy_0(allocatedValue, value, (unsigned int)valueLength);
                allocatedValue[(unsigned int)valueLength] = 0; // Null-terminate the copied value
            }
        }
        else
        {
            // If no value is provided, set memory to null
            allocatedValue = nullptr;
        }

        // Update the reference JSON target with the allocated block
        finalBlockToSet = allocatedValue;
        Json::Value::operator=(jsonTarget, &finalBlockToSet);

        // Handle cleanup based on allocation mode
        if ((char)allocationFlags == 4)
        {
            // If allocation flag requires cleanup and memory exists, free it
            if ((allocationFlags & 0x100) != 0 && finalBlockToSet)
                free(finalBlockToSet);
        }
        else if ((unsigned int)((char)allocationFlags - 6) <= 1) // Specific allocation states
        {
            destructionTarget = (void **)finalBlockToSet;

            // If memory exists for this state, handle dynamic deallocation
            if (finalBlockToSet)
            {
                std::_Tree<std::_Tmap_traits<Json::Value::CZString, Json::Value, std::less<Json::Value::CZString>, std::allocator<std::pair<Json::Value::CZString const, Json::Value>>, 0>>::erase(
                    finalBlockToSet,
                    &temporaryBlock,
                    **(_QWORD **)finalBlockToSet
                );
                operator delete(*destructionTarget, 0x40uLL);
                operator delete(destructionTarget, 0x10uLL);
            }
        }

        // Clean up the key buffer if it exceeds allocated size
        if (simKeyStorage.m128i_i64[1] >= 0x10uLL)
        {
            memorySize = simKeyStorage.m128i_i64[1] + 1;  // Adjusted memory size
            deallocateBlock = keyBuffer[0];

            // Handle corner cases for large memory sizes
            if ((unsigned __int64)(simKeyStorage.m128i_i64[1] + 1) >= 0x1000)
            {
                memorySize = simKeyStorage.m128i_i64[1] + 40;
                deallocateBlock = (char *)*((_QWORD *)keyBuffer[0] - 1);

                if ((unsigned __int64)(keyBuffer[0] - deallocateBlock - 8) > 0x1F)
                    _invalid_parameter_noinfo_noreturn();
            }

            // Deallocate the key buffer
            operator delete(deallocateBlock, memorySize);
        }
    }
}