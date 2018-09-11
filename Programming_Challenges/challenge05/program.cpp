#include <iostream>
#include <unordered_map>
#include <utility>

using namespace std;

long collatzCycle(long, long);
pair<long, long> findMax(long, long);

unordered_map<long, long> COLLATZ_MAP;

int main(int argc, char *argv[]) {
	long lower, upper, val1, val2;
	while (cin >> val1 >> val2) {
		if (val1 < val2) {
			lower = val1;
			upper = val2;
		} else {
			lower = val2;
			upper = val1;
		}
		pair<long, long> maxCycle = findMax(lower, upper);
		cout << val1 << " " << val2 << " " << maxCycle.first << " " << maxCycle.second << endl;
	}
	return 0;
}

pair<long, long> findMax(long lower, long upper) {
	pair<long, long> max (0, -1);
	for (long i = lower; i <= upper; i++) {	
		long cycleLength = collatzCycle(i, 1);
		COLLATZ_MAP[i] = cycleLength;
		if (cycleLength > max.second) {
			max.first = i;
			max.second = cycleLength;
		}
	}
	return max;
}

long collatzCycle(long n, long x) {
	if (n == 1) {
		return x;
	} 
	if (COLLATZ_MAP.count(n)) {
		return (COLLATZ_MAP[n] + x - 1);
    }
    long result;
	if (n%2 != 0) {
		result = collatzCycle(3*n+1, ++x);
	} else {
		result = collatzCycle(n/2, ++x);
	}
    return result;
}
