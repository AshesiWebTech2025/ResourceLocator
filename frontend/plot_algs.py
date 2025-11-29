import pandas as pd
import matplotlib.pyplot as plt
import math

df = pd.read_csv("results.csv")
algs = ["Ford-Fulkerson", "Edmonds-Karp", "Push-Relabel"]
#for each capacity setting and graph_type, plot time vs n
for cap_max in sorted(df['cap_max'].unique()):
    for gtype in sorted(df['graph_type'].unique()):
        subset = df[(df['cap_max'] == cap_max) & (df['graph_type'] == gtype)]
        if subset.empty: continue

        plt.figure(figsize=(8,6))
        for alg in algs:
            s = subset[subset['algorithm'] == alg]
            if s.empty: continue
            # average over trials
            grouped = s.groupby('n')['time_ms'].mean().reset_index()
            plt.plot(grouped['n'], grouped['time_ms'], marker='o', label=alg)

        plt.title(f"Running time vs n — graph_type={gtype}, cap_max={cap_max}")
        plt.xlabel("n (number of vertices)")
        plt.ylabel("time (ms) [avg over trials]")
        plt.legend()
        plt.grid(True)
        plt.savefig(f"time_n_{gtype}_cap{cap_max}.png", dpi=150)
        print("Saved:", f"time_n_{gtype}_cap{cap_max}.png")
        plt.close()

#log-log plot to visualize asymptotic scaling
for cap_max in sorted(df['cap_max'].unique()):
    for gtype in sorted(df['graph_type'].unique()):
        subset = df[(df['cap_max'] == cap_max) & (df['graph_type'] == gtype)]
        if subset.empty: continue

        plt.figure(figsize=(8,6))
        for alg in algs:
            s = subset[subset['algorithm'] == alg]
            if s.empty: continue
            grouped = s.groupby('n')['time_ms'].mean().reset_index()
            plt.loglog(grouped['n'], grouped['time_ms'], marker='o', label=alg)

        plt.title(f"Log-Log Running time vs n — graph_type={gtype}, cap_max={cap_max}")
        plt.xlabel("n (log scale)")
        plt.ylabel("time (ms, log scale)")
        plt.legend()
        plt.grid(True, which='both', ls='--')
        plt.savefig(f"loglog_time_n_{gtype}_cap{cap_max}.png", dpi=150)
        print("Saved:", f"loglog_time_n_{gtype}_cap{cap_max}.png")
        plt.close()
