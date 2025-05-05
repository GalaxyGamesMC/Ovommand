#include <iostream>
#include <vector>
#include <string>

__int64 CommandRegistry_addEnumValues(
    int registry,
    int someParam,
    int **valuesArray
) {
    int *currentValue;
    int *endValue;
    unsigned __int64 valuesCount;
    __int64 resultValue;
    void *tempStorage[2] = {nullptr, nullptr};
    _BYTE *storagePointer = nullptr;
    char tempBuffer[4];
    unsigned int returnCode;

    // Initialize default values
    tempStorage[1] = nullptr;
    storagePointer = nullptr;

    currentValue = valuesArray[1];
    endValue = valuesArray[0];
    valuesCount = (reinterpret_cast<char*>(currentValue) - reinterpret_cast<char*>(endValue)) / 40;

    if (valuesCount > 0) {
        if (valuesCount > 0x666666666666666LL) {
            // Handle excessive size exception
            throw std::length_error("Exceeded maximum size for vector allocation.");
        }

        // Reallocate storage to fit new data
        std::vector<std::pair<std::string, unsigned __int64>> tempVector;
        tempVector.reserve(valuesCount);
        endValue = valuesArray[0];
        currentValue = valuesArray[1];
    }

    // Iterate through data and add enum values
    for (; endValue != currentValue; endValue += 10) {
        __int64 currentEnum = endValue[8];
        std::string enumName(reinterpret_cast<const char*>(endValue));

        if (storagePointer == tempStorage[1]) {
            // Reallocate storage for storing enum pairs
            std::vector<std::pair<std::string, unsigned __int64>>::emplace_back(enumName, currentEnum);
        } else {
            // Store without reallocation
            *reinterpret_cast<std::string*>(tempStorage[1]) = enumName;
            storagePointer = reinterpret_cast<_BYTE*>(tempStorage[1]) + sizeof(std::pair<std::string, unsigned __int64>);
            tempStorage[1] = storagePointer;
        }

        // Clean up temporary string
        enumName.clear();
    }

    // Process enum values into the registry
    returnCode = CommandRegistry_addEnumValuesInternal(
        registry,
        reinterpret_cast<unsigned int>(tempBuffer),
        someParam,
        reinterpret_cast<unsigned int>(tempStorage)
    );

    // Clean up temporary storage
    char *beginStorage = reinterpret_cast<char*>(tempStorage[0]);
    char *endStorage = reinterpret_cast<char*>(tempStorage[1]);

    if (beginStorage != nullptr) {
        while (beginStorage != endStorage) {
            std::string temp;
            beginStorage += 40;
        }

        unsigned __int64 totalBytes = 40 * ((storagePointer - beginStorage) / 40);
        if (totalBytes >= 0x1000) {
            totalBytes += 39;
            beginStorage = reinterpret_cast<char*>(*((_QWORD*)beginStorage - 1));
            if ((unsigned __int64)(storagePointer - beginStorage - 8) > 0x1F) {
                throw std::invalid_argument("Invalid parameter.");
            }
        }

        // Deallocate storage
        ::operator delete(beginStorage, totalBytes);
    }

    return returnCode;
}