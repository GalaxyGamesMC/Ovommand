#include <iostream>
#include <vector>
#include <string>
#include <map>

_DWORD *CommandRegistry_addSoftTerminal(_QWORD *registry, _DWORD *outputValue, void *inputString) {
    __int64 stringComparisonResult;
    _QWORD *registrySymbolsPointer;
    __int64 enumIndex;
    __int64 *treeStructurePointer;
    __int64 rootNode;
    __int64 currentNode;
    __int64 tempNode;
    void *newNode;
    int softTerminalValue;
    size_t deallocationSize;
    void *symbolRangeBuffer = nullptr;

    // Initialize
    registrySymbolsPointer = registry + 24; 
    enumIndex = (__int64)(registry[25] - registry[24]) >> 5;
    treeStructurePointer = registry + 38;
    rootNode = registry[38];
    currentNode = *(_QWORD *)(rootNode + 8);
    tempNode = rootNode;

    // Try to find the existing enum value
    CommandRegistry::findEnumValue(registry, outputValue);
    if (*outputValue) {
        return outputValue; // Found existing value
    }

    // Enum value not found, inserting new one into the tree
    while (!*(_BYTE *)(currentNode + 25)) {
        if (std::string::compare((char *)(currentNode + 32), (const char *)inputString) >= 0) {
            rootNode = currentNode;
            currentNode = *(_QWORD *)currentNode; // Move left
        } else {
            currentNode = *(_QWORD *)(currentNode + 16); // Move right
        }
    }

    if (rootNode == tempNode || std::string::compare((const char *)inputString, (char *)(rootNode + 32)) < 0) {
        stringComparisonResult = (__int64)inputString;
        newNode = std::_Tree<std::map<std::string, unsigned __int64>>::emplace_node(treeStructurePointer, stringComparisonResult);
        std::_Tree<std::map<std::string, unsigned __int64>>::insert_node(treeStructurePointer, newNode);
        rootNode = (__int64)newNode;
    }

    // Associate index value with the newly inserted node
    *(_QWORD *)(rootNode + 64) = enumIndex;

    // Add new string to the registrySymbols list
    if (registrySymbolsPointer[2] == registrySymbolsPointer[1]) {
        std::vector<std::string>::emplace_back((std::vector<std::string> *)registrySymbolsPointer, (const char *)inputString);
    } else {
        std::string newString((const char *)inputString);
        registrySymbolsPointer[1] = (char *)registrySymbolsPointer[1] + sizeof(std::string);
    }

    // Create a soft terminal
    softTerminalValue = enumIndex | 0x2000000;
    _QWORD functionTable[14] = { nullptr };
    functionTable[0] = (void *)&std::_Func_impl_no_alloc<CommandRegistry::ParseToken>::`vftable';
    functionTable[1] = std::move<unsigned int &>;
    functionTable[7] = functionTable;

    *outputValue = softTerminalValue;

    // Construct rules for these symbols
    std::vector<CommandRegistry::Symbol>::_Range_construct_or_tidy(
        &symbolRangeBuffer,
        outputValue,
        reinterpret_cast<char *>(functionTable)
    );

    CommandRegistry::addRule(
        registry, 
        reinterpret_cast<unsigned int *>(&functionTable), 
        reinterpret_cast<unsigned int *>(&symbolRangeBuffer), 
        functionTable, 
        0x7FFFFFFF00000000LL
    );

    // Deallocate resources
    if (symbolRangeBuffer) {
        deallocationSize = ((size_t)(reinterpret_cast<char *>(symbolRangeBuffer) - (char *)symbolRangeBuffer) & 0xFFFFFFFFFFFFFFFCuLL);
        if (deallocationSize >= 0x1000) {
            deallocationSize += 39;
            symbolRangeBuffer = static_cast<void *>(*((_QWORD *)symbolRangeBuffer - 1));
        }
        operator delete(symbolRangeBuffer, deallocationSize);
    }

    return outputValue;
}