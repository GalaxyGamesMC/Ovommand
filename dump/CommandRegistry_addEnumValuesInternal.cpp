#include <iostream>
#include <vector>
#include <string>

__int64 CommandRegistry_addEnumValuesInternal(
    __int64 registry, 
    __int64 param2, 
    __int64 param3, 
    void ***enumValues, 
    __int16 *shortValue, 
    __int64 param6
) {
    void **currentValue;
    void **endValue;
    unsigned __int64 enumCount;
    size_t stringSize;
    void *tempValue;
    void *storageToDelete;
    unsigned __int64 deleteSize;
    __int16 temporaryShort;

    void *storageArray[2] = {nullptr, nullptr};
    void *storagePointer = nullptr;

    // Initialize enum value pointers
    currentValue = enumValues[0];
    endValue = enumValues[1];
    enumCount = (reinterpret_cast<char*>(endValue) - reinterpret_cast<char*>(currentValue)) / 40;

    if (enumCount > 0) {
        if (enumCount > 0xFFFFFFFFFFFFFFFLL) {
            // Throw exception if enumCount exceeds large limit
            throw std::length_error("Enum count exceeds maximum allowable size.");
        }

        // Reallocate storage to handle enums
        std::vector<mce::UUID> temporaryStorage;
        temporaryStorage.reserve(enumCount);
        currentValue = enumValues[0];
        endValue = enumValues[1];
    }

    // Process each enum value
    while (currentValue != endValue) {
        _BYTE *stringData = (_BYTE *)currentValue;
        if ((unsigned __int64)currentValue[3] >= 16) {
            stringData = (_BYTE *)*currentValue;
        }

        std::string tempString(stringData);
        temporaryShort = *shortValue;

        // Add the soft terminal for the current enum value
        CommandRegistry_addSoftTerminal(registry, &temporaryShort, tempString);

        // Process and store the enum data
        if (storagePointer == storageArray[1]) {
            std::vector<FunctionManager::QueuedCommand>::emplace_back(tempString);
        } else {
            *reinterpret_cast<void**>(storageArray[1]) = static_cast<void*>(&tempString);
            storageArray[1] = (char*)storageArray[1] + sizeof(tempString);
        }

        // Clean up storage if needed
        if (tempString.size() >= 16) {
            tempValue = static_cast<void*>(&tempString);
            deleteSize = tempString.size() + 1;
            if (tempString.size() + 1 >= 0x1000) {
                deleteSize = tempString.size() + 40;
                tempValue = static_cast<void*>(*((_QWORD*)tempString.data() - 1));
                if ((unsigned __int64)(tempString.data() - (char*)tempValue - 8) > 0x1F) {
                    throw std::invalid_argument("Invalid storage allocation.");
                }
            }
            operator delete(tempValue, deleteSize);
        }

        currentValue += 5; // Move to the next enum value
    }

    // Recursive call to process internal details
    ((void(__fastcall*)(__int64, __int64, __int64, void ***, __int16 *, __int64))
        CommandRegistry_addEnumValuesInternal)(registry, param2, param3, enumValues, shortValue, param6);

    // Clean up allocated resources
    if (storageArray[0]) {
        deleteSize = ((unsigned __int64)storagePointer - (unsigned __int64)storageArray[0]) & 0xFFFFFFFFFFFFFFF0uLL;
        if (deleteSize >= 0x1000) {
            deleteSize += 39;
            storageToDelete = *((void **)storageArray[0] - 1);
            if ((unsigned __int64)((char *)storageArray[0] - (char *)storageToDelete - 8) > 0x1F) {
                throw std::invalid_argument("Incorrect allocation size.");
            }
        }
        operator delete(storageArray[0], deleteSize);
    }

    // Return the second parameter as the result
    return param2;
}