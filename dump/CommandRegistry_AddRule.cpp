__int64 __fastcall CommandRegistry::addRule(
    _QWORD *registry, // Pointer to the command registry
    int symbol,       // Command symbol (reference to the base command or identifier)
    int symbolList,   // List of command symbols or arguments
    __int64 parseTable, // Parse table or other registry-specific data to update
    __int64 versionRange // Version range data for the rule
) {
    __int64 currentRulePtr;  // Pointer to the current list of ParseRules
    __int64 tempResult;      // Temporary result item during iteration
    _QWORD *parseTableList;  // Pointer to parse table list
    __int64 *parseTableStart; // Start of the parse table
    __int64 result;          // Used as the result of the function
    int minVersion;          // Minimum version from versionRange
    int maxVersion;          // Maximum version from versionRange
    int ruleVersion;         // Version of a specific rule being checked
    __int64 rightChild;      // Pointer to the right child in a binary tree representation
    __int64 leftChild;       // Pointer to the left child in a binary tree representation
    __int64 extractedRule;   // Pointer to an extracted rule for deletion
    __int64 parseTreePtr;    // Pointer to the parse tree entry for cleaning

    currentRulePtr = registry[17]; // Get the current list of ParseRules
    
    // Check if we need to reallocate memory for ParseRules
    if (registry[18] == currentRulePtr) {
        std::vector<CommandRegistry::ParseRule>::_Emplace_reallocate<
            CommandRegistry::Symbol &,
            std::vector<CommandRegistry::Symbol>,
            std::function<CommandRegistry::ParseToken *(
                CommandRegistry::ParseToken &, CommandRegistry::Symbol)> &,
            CommandVersion &>(
            (_DWORD)registry + 128, currentRulePtr, symbol, symbolList, parseTable, (__int64)&versionRange);
    } else {
        // Simply construct a new ParseRule in the existing memory
        std::_Default_allocator_traits<std::allocator<CommandRegistry::ParseRule>>::construct<
            CommandRegistry::ParseRule,
            CommandRegistry::Symbol &,
            std::vector<CommandRegistry::Symbol>,
            std::function<CommandRegistry::ParseToken *(
                CommandRegistry::ParseToken &, CommandRegistry::Symbol)> &,
            CommandVersion &>(
            (_DWORD)registry, currentRulePtr, symbol, symbolList, parseTable, (__int64)&versionRange);
        registry[17] += 104LL; // Move to the next rule slot (rules of size 104 bytes)
    }

    // Prepare to clean up outdated rules in the parse table
    parseTableList = registry + 19;       // Pointer to ParseTable list
    parseTableStart = (__int64 *)registry[19]; // Start of the parse table

    result = *parseTableStart;
    if ((__int64 *)*parseTableStart != parseTableStart) {
        minVersion = versionRange;       // Extract min version from versionRange
        maxVersion = HIDWORD(versionRange); // Extract max version from versionRange

        // Iterate through the parse table to handle outdated versions
        do {
            ruleVersion = *(_DWORD *)(result + 32); // Get the version of the rule
            if (ruleVersion < minVersion || ruleVersion > maxVersion) {
                // If version is outside the desired range
                rightChild = *(_QWORD *)(result + 16);
                if (*(_BYTE *)(rightChild + 25)) {
                    for (leftChild = *(_QWORD *)(result + 8); !*(_BYTE *)(leftChild + 25); leftChild = *(_QWORD *)(leftChild + 8)) {
                        if (result != *(_QWORD *)(leftChild + 16))
                            break;
                        result = leftChild;
                    }
                    result = leftChild;
                } else {
                    result = *(_QWORD *)(result + 16);
                    for (tempResult = *(_QWORD *)rightChild; !*(_BYTE *)(tempResult + 25); tempResult = *(_QWORD *)tempResult)
                        result = tempResult;
                }
            } else {
                // Version is within range, so remove this rule
                parseTreePtr = result;
                rightChild = *(_QWORD *)(result + 16);
                if (*(_BYTE *)(rightChild + 25)) {
                    for (rightChild = *(_QWORD *)(result + 8); !*(_BYTE *)(rightChild + 25); rightChild = *(_QWORD *)(rightChild + 8)) {
                        if (parseTreePtr != *(_QWORD *)(rightChild + 16))
                            break;
                        parseTreePtr = rightChild;
                    }
                } else {
                    for (leftChild = *(_QWORD *)rightChild; !*(_BYTE *)(leftChild + 25); leftChild = *(_QWORD *)leftChild)
                        rightChild = leftChild;
                }

                // Extract and delete the outdated rule
                extractedRule = std::_Tree_val<std::_Tree_simple_types<std::pair<std::string const,unsigned __int64>>>::_Extract(
                    parseTableList, result);
                CommandRegistry::ParseTable::~ParseTable((CommandRegistry::ParseTable *)(extractedRule + 40));
                operator delete((void *)extractedRule, 0x58uLL); // Free memory for the extracted rule
                result = rightChild;
            }
        } while (result != *parseTableList); // End iteration
    }

    // Clean up parsing tree associated with the current table if needed
    parseTreePtr = *(_QWORD *)(parseTable + 56);
    if (parseTreePtr) {
        LOBYTE(tempResult) = parseTreePtr != parseTable;
        result = (*(__int64 (__fastcall **)(__int64, __int64))(*(_QWORD *)parseTreePtr + 32LL))(parseTreePtr, tempResult);
        *(_QWORD *)(parseTable + 56) = 0LL; // Reset parse table association
    }

    return result; // Return the final result (possibly indicating success or status)
}