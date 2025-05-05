unsigned __int64 __fastcall CommandRegistry::getEnumData(
    CommandRegistry *this,                             // Pointer to the current CommandRegistry instance.
    const struct CommandRegistry::ParseToken *a2       // Pointer to a ParseToken structure which contains the information needed to resolve the enum.
)
{
    unsigned __int64 v2; // Temporary variable holding a masked value derived from a field in the token.
    unsigned __int64 v3; // Hash/index value computed from the masked token data.
    __int64 v4;          // Base pointer to the data structure where enum mappings are stored.
    __int64 v5;          // Start pointer used in a binary search to locate the resolved enum value.
    __int64 v6;          // The remaining range (or search space) size during the binary search.

    // Extract a masked value from the ParseToken, located at an offset of +36 in the token data structure.
    // The mask is applied to limit the bits (possibly to isolate specific encoded data).
    v2 = *(int *)(*(_QWORD *)a2 + 36LL) & 0xFFFFFFFFF80FFFFFuLL;

    // Perform another masked operation on a field in the ParseToken, specifically the field at offset +9.
    // Multiply this masked result by 9 to calculate an index or offset used for further lookup.
    v3 = 9 * (*((int *)a2 + 9) & 0xFFFFFFFFF80FFFFFuLL);

    // Access an internal mapping table in the CommandRegistry object.
    // This table is assumed to contain data structures or arrays for enum resolution.
    v4 = *((_QWORD *)this + 27);

    // Get the starting pointer (v5) and range size (v6) for performing a binary search.
    v5 = *(_QWORD *)(v4 + 8 * v3 + 48);                  // Start of the search range.
    v6 = (*(_QWORD *)(v4 + 8 * v3 + 56) - v5) >> 4;      // Number of possible entries in the range (size / 16 bytes per entry).

    // Perform a binary search to find the enum value that matches the masked value `v2`.
    while (v6 > 0) // While there are elements left to search in the range...
    {
        // Compare the current middle value in the search range with v2.
        if (*(_QWORD *)(v5 + 16 * (v6 >> 1)) >= v2) 
        {
            v6 >>= 1; // Narrow the search range to the first half if the middle value is greater or equal to v2.
        }
        else 
        {
            // Move the start pointer (v5) forward to the second half of the remaining range.
            v5 += 16 * (v6 >> 1) + 16;

            // Reduce the range size by accounting for the split and the middle element.
            v6 += -1 - (v6 >> 1);
        }
    }

    // At this point, v5 should point to the memory location holding the resolved enum data.
    // Return the resolved enum value stored at (v5 + 8).
    return *(_QWORD *)(v5 + 8);
}