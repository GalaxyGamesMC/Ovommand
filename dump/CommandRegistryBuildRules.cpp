_DWORD *__fastcall CommandRegistry::buildRules(
    _QWORD *registry,                 // Pointer to the command registry
    _DWORD *outputRuleId,             // Pointer where the resulting rule ID is stored
    __int64 parseTable,               // Parse Table or current rule being processed
    unsigned __int64 *symbolRange,    // Range of symbols to process and match against rules
    unsigned __int64 symbolIndex      // Index within symbols to build rules for
) {
    __int64 remainingSymbols;           // Remaining symbols to process
    _DWORD *resultPtr;                  // Pointer to result (return value)
    char allSymbolsMatch = false;       // Flag indicating if all symbols matched existing rules
    char noOptionalMatches = true;      // Flag for optional match (all symbols required)
    unsigned __int64 symbolStart;       // Start address for symbol range
    unsigned __int64 symbolCount;       // Number of symbols to process
    __int64 currentSymbolIdx;           // Current symbol index being inspected
    unsigned __int64 blockCount;        // Total processed blocks
    __int64 symbolDataPtr;              // Address of symbol data
    _QWORD *symbolTablePtr;             // Pointer to the symbol table
    _DWORD *symbolListEnd;              // End of the symbol list
    __int64 processedSymbols;           // Processed symbols from the range

    // Initialize variables
    currentSymbolIdx = 0LL;  // Start with the first symbol
    resultPtr = outputRuleId;
    symbolStart = *symbolRange;
    blockCount = (symbolRange[1] - symbolStart + 7) >> 3; // Compute the total number of blocks

    // Validate symbol range
    if (symbolStart > symbolRange[1]) {
        blockCount = 0LL;
    }

    // Loop through each block of symbols to validate against rules
    if (blockCount) {
        do {
            symbolDataPtr = *(_QWORD *)(*(_QWORD *)symbolStart + 16LL); // Load symbol data

            // Check if the symbol data references the specific rule index
            if ((*(_QWORD *)(*(_QWORD *)symbolStart + 24LL) - symbolDataPtr) / 80 == symbolIndex) {
                allSymbolsMatch = true; // Match found
            } else {
                noOptionalMatches = false; // No complete match
                if (*(_BYTE *)(symbolDataPtr + 80 * symbolIndex + 72)) {
                    allSymbolsMatch = true; // Match found but optional
                }
            }

            // Move to the next block
            symbolStart += 8LL;
            ++currentSymbolIdx;
        } while (currentSymbolIdx != blockCount);

        // If all symbols matched and no optional matches exist
        if (allSymbolsMatch) {
            if (noOptionalMatches) {
                *outputRuleId = 0x100000; // Set rule ID as successful
                return resultPtr;
            }
        }
    }

    // Otherwise, analyze and build alternative or partial rules
    symbolTablePtr = registry + 30;                   // Base pointer to symbol table
    symbolListEnd = (_DWORD *)registry[31];           // End pointer of symbol list
    processedSymbols = ((__int64)symbolListEnd - registry[30]) >> 2; // Processed symbols count

    int ruleVersion = processedSymbols | 0x900000;    // Assign rule version details
    if (*(_DWORD *)(parseTable + 108) == -1) {        // Check if rule version is unset
        *(_DWORD *)(parseTable + 108) = processedSymbols;
    }

    int symbolData = *(_DWORD *)(parseTable + 92);    // Extract symbol data
    if ((_DWORD *)registry[32] == symbolListEnd) {    // Check if the symbol list needs reallocation
        std::vector<CommandRegistry::Factorization>::_Emplace_reallocate<
            CommandRegistry::Factorization>(symbolTablePtr, symbolListEnd, &symbolData);
    } else {
        *symbolListEnd = symbolData;                  // Append symbol data
        registry[31] += 4LL;                          // Increment end of symbol list
    }

    if (allSymbolsMatch) {
        // ... Code for handling matched symbols and recursive rule additions ...
        // The logic here involves building rules recursively, validating against existing 
        // rule trees, and ensuring optional or alternative parsing chains are fitted.
    }

    // Cleanup and memory release
    resultPtr = outputRuleId;
    *resultPtr = ruleVersion;

    return resultPtr;
}