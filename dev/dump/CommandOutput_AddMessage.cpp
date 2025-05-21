/**
 * Adds a message to CommandOutput
 * @param output CommandOutput instance
 * @param message Message content
 * @param params Message parameters
 * @param type Message type
 */
void* __fastcall CommandOutput::addMessage(
    __int64 output,
    void* message,
    _QWORD* params,
    int type
) {
    // Clear existing messages for error output type
    if (*(_DWORD*)output == 1) {
        auto messages = *(GameRule::ValidationError**)(output + 16);
        auto messagesEnd = *(GameRule::ValidationError**)(output + 24);
        if (messages != messagesEnd) {
            std::_Destroy_range<std::allocator<CommandOutputMessage>>(messages);
            *(_QWORD*)(output + 24) = *(_QWORD*)(output + 16);
        }
    }

    // Process parameters
    void* paramStorage[2] = { nullptr, nullptr };
    char* paramEnd = nullptr;
    size_t paramCount = (params[1] - *params) / 40;
    
    if (paramCount) {
        if (paramCount > 0x7FFFFFFFFFFFFFF) {
            std::vector<JsonUtil::SchemaMatchedNodePtr<JsonUtil::EmptyClass,DragonStrafePlayerDefinition>>::_Xlength();
        }
        std::vector<std::string>::_Reallocate_exactly(paramStorage);
        
        // Copy valid parameters
        auto paramPtr = (_DWORD*)*params;
        auto paramEndPtr = (_DWORD*)params[1];
        auto destPtr = (char*)paramStorage[1];
        
        while (paramPtr != paramEndPtr) {
            if (paramPtr[8] != -1) {
                if (destPtr == paramEnd) {
                    std::vector<std::string>::_Emplace_reallocate<std::string const&>(
                        paramStorage, destPtr, paramPtr);
                    destPtr = (char*)paramStorage[1];
                } else {
                    std::string::string(destPtr, paramPtr);
                    destPtr += 32;
                    paramStorage[1] = destPtr;
                }
            }
            paramPtr += 10;
        }
    }

    // Add message to output
    void* result;
    auto currentMsg = *(_QWORD*)(output + 24);
    
    if (currentMsg == *(_QWORD*)(output + 32)) {
        result = (void*)std::vector<CommandOutputMessage>::_Emplace_reallocate<
            enum CommandOutputMessageType&,
            std::string const&,
            std::vector<std::string>>(
                output + 16,
                currentMsg,
                (unsigned int*)&type,
                (_DWORD)message,
                (__int64)paramStorage);
    } else {
        *(_DWORD*)currentMsg = type;
        std::string::string((void*)(currentMsg + 8), message);
        
        // Move parameter storage
        *(_QWORD*)(currentMsg + 40) = paramStorage[0];
        *(_QWORD*)(currentMsg + 48) = paramStorage[1];
        *(_QWORD*)(currentMsg + 56) = paramEnd;
        paramStorage[0] = paramStorage[1] = nullptr;
        paramEnd = nullptr;
        
        *(_QWORD*)(output + 24) += 64;
        result = nullptr;
    }

    // Cleanup parameter storage
    if (paramStorage[0]) {
        std::_Destroy_range<std::allocator<std::string>>(
            paramStorage[0], paramStorage[1], paramStorage);
            
        void* toFree = paramStorage[0];
        if (((paramEnd - (char*)paramStorage[0]) & 0xFFFFFFFFFFFFFFE0ull) >= 0x1000) {
            toFree = (void*)*(((_QWORD*)paramStorage[0]) - 1);
            if ((unsigned __int64)((char*)paramStorage[0] - (char*)toFree - 8) > 0x1F) {
                _invalid_parameter_noinfo_noreturn();
            }
        }
        return std::_Return_temporary_buffer<unsigned int>(toFree);
    }

    return result;
}