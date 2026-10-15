
import json
import pandas as pd
import matplotlib.pyplot as plt

data = []

with open("hasil-test.json", encoding="utf-8") as file:
    for line in file:
        try:
            item = json.loads(line)
            if item.get("type") != "Point":
                continue

            metric = item.get("metric")
            point = item.get("data", {})
            values = point.get("data", {})
            timestamp = values.get("time")
            value = values.get("value")

            if timestamp is not None and value is not None:
                data.append({
                    "time": pd.to_datetime(timestamp),
                    "metric": metric,
                    "value": value,
                })
        except (json.JSONDecodeError, TypeError):
            pass

df = pd.DataFrame(data)

if df.empty:
    print("Data tidak ditemukan. Periksa format hasil-test.json.")
    raise SystemExit

charts = [
    ("http_req_duration", "Waktu respons (ms)"),
    ("http_reqs", "Request per detik"),
    ("vus", "Virtual users"),
]

for metric, title in charts:
    subset = df[df["metric"] == metric].sort_values("time")

    if subset.empty:
        print(f"Metrik {metric} tidak ditemukan.")
        continue

    plt.figure(figsize=(11, 5))
    plt.plot(subset["time"], subset["value"])
    plt.title(title)
    plt.xlabel("Waktu")
    plt.ylabel(title)
    plt.grid(True)
    plt.xticks(rotation=30)
    plt.tight_layout()
    plt.savefig(f"{metric}.png")
    plt.show()
