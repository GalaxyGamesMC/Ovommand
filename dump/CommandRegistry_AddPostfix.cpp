_DWORD *__fastcall CommandRegistry::addPostfix(
    _QWORD *registry,   // Registry instance
    _DWORD *ruleOut,    // Pointer to store the result rule ID
    __int64 ruleDataPtr // Pointer to the rule data (input string or structure being processed)
) {
    _OWORD *ruleData;               // Pointer to the rule data (complex data type)
    unsigned __int64 ruleLength;    // Actual length of the rule string
    unsigned __int64 bufferLength;  // Memory buffer size to handle the rule string
    void **bufferStart;             // Beginning of the allocated buffer
    void **bufferPointer;           // Pointer to the loaded string buffer
    unsigned __int64 editStringLen; // Adjusted string length after transformations
    void **caseInsensitiveBuffer;  // Case-insensitive version of the string
    signed __int64 bufferSize;      // Difference between end and start of buffer
    __int64 i;                      // Loop iterator
    size_t *existingRuleEnd;        // End of the existing rules in the registry
    size_t *existingRuleStart;      // Start of the existing rules in the registry
    size_t *currentRule;            // Current rule being checked during the loop
    void **currentBuffer;           // Pointer to the transformed buffer
    size_t currentBufferSize;       // Size of the current buffer being compared
    const void *compareBuffer;      // Buffer being compared during validation
    _DWORD *outputRuleId;           // Pointer to store resulting rule ID
    unsigned __int64 largeAllocSize; // Adjusted size for large allocations
    void **deleteBufferStart;        // Pointer to the beginning of the buffer to delete
    void **ruleBufferPointer;        // Pointer to the rule's actual buffer
    int finalRuleId;                 // Final rule ID to assign
    _BYTE *largeDeleteBuffer;        // For deleting large allocations
    unsigned __int64 largeDeleteSize; // For size of large allocations
    int tempRuleId;                  // Temporary storage for rule ID
    char tempCharBuffer[4];          // Temporary character buffer
    _QWORD *tempStruct;              // Temporary structure instance
    void *tempAlloc;                 // Temporary allocation
    __int128 tempAllocMeta;          // Metadata for the temporary allocation
    __int64 ruleFlags;               // Rule flags or status
    _QWORD tempSymbolArray[8];       // Temporary array for handling symbols
    void *finalBuffer[2];            // Final buffer for new rule construction
    unsigned __int64 finalBufferLength; // Length of the final buffer
    unsigned __int64 finalRuleLength;   // Length of the final rule string

    // Initialize variables
    ruleFlags = -2LL;
    ruleData = (_OWORD *)ruleDataPtr;
    outputRuleId = ruleOut;
    tempStruct = registry;
    finalBufferLength = 0LL;
    finalRuleLength = 0LL;

    // Retrieve rule string length
    ruleLength = *(_QWORD *)(ruleDataPtr + 16);

    // If data is stored in a heap or secondary allocation
    if (*(_QWORD *)(ruleDataPtr + 24) >= 0x10uLL) {
        ruleData = *(_OWORD **)ruleDataPtr;
    }

    // Handle allocation and processing
    if (ruleLength >= 0x10) {
        bufferLength = ruleLength | 0xF;
        if ((ruleLength | 0xF) > 0x7FFFFFFFFFFFFFFFLL)
            bufferLength = 0x7FFFFFFFFFFFFFFFLL;
        finalBuffer[0] = (void *)std::_Allocate<16, std::_Default_allocate_traits, 0>(bufferLength + 1);
        memcpy_0(finalBuffer[0], ruleData, ruleLength + 1);
        bufferStart = finalBuffer;
        bufferPointer = (void **)finalBuffer[0];
        if (bufferLength >= 0x10)
            bufferStart = (void **)finalBuffer[0];
    } else {
        *(_OWORD *)finalBuffer = *ruleData;
        bufferLength = 15LL;
        bufferStart = finalBuffer;
        bufferPointer = (void **)finalBuffer[0];
    }

    finalRuleLength = bufferLength;
    finalBufferLength = ruleLength;
    editStringLen = ruleLength;

    // Prepare a case-insensitive version of the buffer for comparison purposes
    caseInsensitiveBuffer = finalBuffer;
    if (bufferLength >= 0x10)
        caseInsensitiveBuffer = bufferPointer;

    bufferSize = (char *)caseInsensitiveBuffer + ruleLength - (char *)caseInsensitiveBuffer;
    if (caseInsensitiveBuffer > caseInsensitiveBuffer + ruleLength)
        bufferSize = 0LL;

    if (bufferSize) {
        for (i = 0; i != bufferSize; ++i)
            *((_BYTE *)bufferStart + i) = tolower(*((char *)caseInsensitiveBuffer + i));
        bufferLength = finalRuleLength;
        editStringLen = finalBufferLength;
        bufferPointer = (void **)finalBuffer[0];
    }

    // Check against existing rules in the registry
    existingRuleEnd = (size_t *)registry[34];
    existingRuleStart = (size_t *)registry[33];
    for (currentRule = existingRuleStart; currentRule != existingRuleEnd; currentRule += 4) {
        currentBuffer = finalBuffer;
        if (bufferLength >= 0x10)
            currentBuffer = bufferPointer;
        currentBufferSize = currentRule[2];
        compareBuffer = currentRule;
        if (currentRule[3] >= 0x10)
            compareBuffer = (const void *)*currentRule;
        if (currentBufferSize == editStringLen && !memcmp_0(compareBuffer, currentBuffer, currentBufferSize))
            break;
    }

    if (currentRule == existingRuleEnd) {
        // Add the rule if it's new
        if ((size_t *)registry[35] == existingRuleEnd) {
            std::vector<std::string>::_Emplace_reallocate<std::string const &>(registry + 33, existingRuleEnd, finalBuffer);
        } else {
            std::string::string(existingRuleEnd, finalBuffer);
            registry[34] += 32LL;
        }

        finalRuleId = (((char *)existingRuleEnd - (char *)existingRuleStart) >> 5) | 0x1000000;
        ruleFlags = 0x7FFFFFFF00000000LL;

        // Temporary for rule registration
        tempStruct = tempSymbolArray;
        tempSymbolArray[0] = &std::_Func_impl_no_alloc<CommandRegistry::ParseToken * (*)(
                              CommandRegistry::ParseToken &, CommandRegistry::Symbol),
                              CommandRegistry::ParseToken *, CommandRegistry::ParseToken &,
                              CommandRegistry::Symbol>::`vftable';
        tempSymbolArray[1] = std::move<unsigned int &>;
        tempSymbolArray[7] = tempSymbolArray;
        tempRuleId = finalRuleId;
        tempAlloc = 0LL;
        tempAllocMeta = 0LL;

        // Add the rule
        std::vector<CommandRegistry::Symbol>::_Range_construct_or_tidy<CommandRegistry::Symbol const *>(&tempAlloc, &tempRuleId, tempCharBuffer);
        LODWORD(tempStruct) = 1048607;
        CommandRegistry::addRule((_DWORD)registry, (unsigned int)&tempStruct, (unsigned int)&tempAlloc, (unsigned int)tempSymbolArray, ruleFlags);

        // Cleanup
        largeDeleteBuffer = tempAlloc;
        if (tempAlloc) {
            largeDeleteSize = 4 * ((__int64)(*((_QWORD *)&tempAllocMeta + 1) - (_QWORD)tempAlloc) >> 2);
            if (largeDeleteSize >= 0x1000) {
                largeDeleteSize += 39LL;
                largeDeleteBuffer = (_BYTE *)*((_QWORD *)tempAlloc - 1);
                if ((unsigned __int64)((_BYTE *)tempAlloc - largeDeleteBuffer - 8) > 0x1F)
                    _invalid_parameter_noinfo_noreturn();
            }
            operator delete(largeDeleteBuffer, largeDeleteSize);
        }

        // Return the rule ID
        outputRuleId = ruleOut;
        *ruleOut = finalRuleId;
        if (finalRuleLength >= 0x10) {
            largeAllocSize = finalRuleLength + 1;
            deleteBufferStart = (void **)finalBuffer[0];
            if (finalRuleLength + 1 >= 0x1000) {
                largeAllocSize = finalRuleLength + 40;
                deleteBufferStart = (void **)*((_QWORD *)finalBuffer[0] - 1);
                if ((unsigned __int64)((char *)finalBuffer[0] - (char *)deleteBufferStart - 8) > 0x1F)
                    _invalid_parameter_noinfo_noreturn();
            }
            operator delete(deleteBufferStart, largeAllocSize);
        }
    } else {
        outputRuleId = ruleOut;
        *ruleOut = (((char *)currentRule - (char *)existingRuleStart) >> 5) | 0x1000000;
        if (bufferLength >= 0x10) {
            largeAllocSize = bufferLength + 1;
            ruleBufferPointer = bufferPointer;
            if (bufferLength + 1 >= 0x1000) {
                largeAllocSize = bufferLength + 40;
                bufferPointer = (void **)*(bufferPointer - 1);
                if ((unsigned __int64)((char *)ruleBufferPointer - (char *)bufferPointer - 8) > 0x1F)
                    _invalid_parameter_noinfo_noreturn();
            }
            operator delete(bufferPointer, largeAllocSize);
        }
    }
    return outputRuleId;
}