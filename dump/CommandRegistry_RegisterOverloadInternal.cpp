 // Registers a specific overload (a3) for a given command signature (a2) in the command registry.
// The function also enables caching, validation, and memory management for the provided overload.
void __fastcall CommandRegistry::registerOverloadInternal(
    CommandRegistry *this,                             // The current CommandRegistry instance responsible for managing commands and overloads.
    struct CommandRegistry::Signature *a2,            // Pointer to the signature of the command being extended with the new overload.
    struct CommandRegistry::Overload *a3              // Pointer to the overload definition being registered.
)
{
    CommandRegistry *v3;                             // Cache `this` for reuse.
    unsigned __int16 *v4;                            // Pointer to the first parameter in the overload being processed.
    __int16 *v5;                                     // Pointer to the last parameter of the overload.
    struct CommandRegistry::Overload *v6;           // Used for overload validation and tree traversal.
    __int64 *v7;                                     // Traverses a tree-like structure to locate matching overloads.
    unsigned __int16 v8;                             // Current parameter value.
    _QWORD *v9;                                      // Parameter cache lookup.
    _BYTE *v10;                                      // Points to cached parameter data.
    int v11;                                         // Current parameter state (e.g., validated or not).
    size_t v12;                                      // Length of the parameter value.
    __int64 *v13, *v14, *v15;                        // Iterators for parameter searches.
    unsigned __int64 v16;                            // Metadata about the parameter size.
    size_t v17;                                      // Metadata about the parameter length.
    void **v18;                                      // Buffer holding string representations of the parameters.
    size_t Size;                                     // Tracks size information used for string handling.
    unsigned __int64 v56;                            // Additional metadata (e.g., parameter constraints or rules).
    struct CommandRegistry::Signature *v59;          // Used to validate and build rules for the command signature.

    // Initialize key variables for processing
    v59 = a2;                     // Store the provided command signature (a2) for validation.
    v3 = this;                    // Cache `this` for reuse.
    v57 = this;
    v4 = (unsigned __int16 *)*((_QWORD *)a3 + 2);  // Pointer to the overload's parameter list (start of parameters).
    v5 = (__int16 *)*((_QWORD *)a3 + 3);          // Pointer to the end of the overload's parameter list.

    // Loop through all parameters of the current overload.
    // This involves validating and registering each parameter.
    if (v4 != (unsigned __int16 *)v5) {
        while (1) {
            // If the parameter is not yet validated...
            if (!*((_DWORD *)v4 + 15)) {
                a3 = (struct CommandRegistry::Overload *)*((_QWORD *)v3 + 45);  // Root of the command registry's overloads.
                v6 = a3;
                v7 = (__int64 *)*((_QWORD *)a3 + 1);  // Tree traversal starts here.

                // Traverse the command registry tree to find a matching overload for the current parameter.
                if (*((unsigned char *)v7 + 25)) goto LABEL_OVERLOAD_FOUND;

                v8 = *v4;  // Get the current parameter value.

                // Binary search through the tree to locate the corresponding overload for this parameter.
                do {
                    if (*((unsigned short *)v7 + 14) >= v8) {
                        v6 = (struct CommandRegistry::Overload *)v7;  // Update potential match.
                        v7 = (__int64 *)*v7;                          // Move to the left child.
                    } else {
                        v7 = (__int64 *)v7[2];  // Move to the right child.
                    }
                } while (!*((unsigned char *)v7 + 25));

                // If no match was found...
                if (v6 == a3 || v8 < *((unsigned short *)v6 + 14))
LABEL_OVERLOAD_FOUND:
                    v6 = (struct CommandRegistry::Overload *)*((_QWORD *)v3 + 45);

                // Finalize parameter validation for the current overload if valid.
                if (v6 != a3 && *((_DWORD *)v6 + 8) != -1) {
                    *((_DWORD *)v4 + 15) = 1;  // Mark the parameter as validated.

                    // Cache the parameter data for faster lookup.
                    v9 = (_QWORD *)(*((_QWORD *)v3 + 27) + 72LL * *((int *)v6 + 8));
                    if (v9[3] >= 0x10uLL) v9 = (_QWORD *)*v9;
                    *((_QWORD *)v4 + 6) = v9;
                }

                // Additional checks for command parsing logic.
                v5 = v53;  // Restore `v5` to avoid discrepancies.
                if (*((__int64 (__fastcall **)(int, int, int, int, int, void *, __int64))v4 + 1) == CommandRegistry::parse<std::unique_ptr<Command>>) {
                    *((_BYTE *)v4 + 73) |= 2u;  // Update flags to indicate parsing behavior.
                }
            }

            // Validate cached parameter data.
            v10 = (_BYTE *)*((_QWORD *)v4 + 6);  // Get the cached parameter value.
            if (!v10) goto LABEL_FINAL;

            v11 = *((_DWORD *)v4 + 15);  // Check the current parameter's validation state.

            // Handle string parameters.
            Size = 0LL;
            v56 = 15LL;
            LOBYTE(Buf2[0]) = 0;
            v12 = -1LL;
            if (v11 != 1) break;  // Only process parameters in a validated state.

            // Get the length of the string parameter.
            do ++v12;
            while (v10[v12]);

            // Copy and process the string parameter.
            std::string::assign(Buf2, v10, v12);

            // Validate and store the parameter in the command registry's parameter list.
            v13 = (__int64 *)*((_QWORD *)v3 + 36);  // Access the parameter validation tree.
            v14 = (__int64 *)v13[1];
            v15 = v13;
            v16 = v56;
            v17 = Size;
            v18 = (void **)Buf2[0];

            // Perform a binary lookup to ensure no duplicates.
            if (!*((_BYTE *)v14 + 25)) {
                do {
                    // Binary tree search for parameter uniqueness.
                    v19 = v14 + 4;
                    v20 = Buf2;
                    if (v16 >= 0x10) v20 = v18;
                    v21 = v14[6];
                    if ((unsigned __int64)v14[7] >= 0x10) v19 = (_QWORD *)*v19;
                    v22 = v14[6];
                    if (v17 < v21) v22 = v17;
                    v23 = memcmp_0(v19, v20, v22);  // Compare parameters.

                    // Adjust the position in the binary tree based on the comparison result.
                    if (!v23) {
                        if (v21 >= v17)
                            v23 = v21 > v17;
                        else
                            v23 = -1;
                    }
                    if (v23 >= 0) {
                        v13 = v14;
                        v14 = (__int64 *)*v14;
                    } else {
                        v14 = (__int64 *)v14[2];
                    }
                } while (!*((_BYTE *)v14 + 25));

                v15 = (__int64 *)*((_QWORD *)v57 + 36);  // Restore root.
            }

            // If no match was found, assign a unique parameter ID.
            if (v13 == v15) goto LABEL_ASSIGN_PARAMETER;
            v24 = v13 + 4;  // Update and validate position.
            continue;
        }
    }

LABEL_FINAL:
    CommandRegistry::setupOverloadRules(v3, v59, a3);  // Finalize rules and registration for the overload.
}